<?php
namespace App\Services\Sale;

use App\Exceptions\CannotCancelSaleException;
use App\Exceptions\CompletedOrExpiredSaleException;
use App\Exceptions\NotFoundSKUException;
use App\Exceptions\InsufficientStockException;

use App\Data\PaymentData;

use App\Enums\DocumentPrefix;
use App\Enums\SaleStatus;

use App\Models\Branch;
use App\Models\MeasureUnit;
use App\Models\ProductSku;
use App\Models\Sale;
use App\Models\StockLocation;

use App\Services\Document\NumberingService;
use App\Services\Inventory\StockService;

use Illuminate\Support\Facades\DB;

class SaleService
{
    public function __construct(
        protected NumberingService $numberingService,
        protected StockService $stockService
    ) {}

    public function create(array $data): Sale
    {
        return DB::transaction(function () use ($data) {
            $unitIds = collect($data['items'])->pluck('measure_unit_id')->unique();
            $units = MeasureUnit::whereIn('id', $unitIds)->get()->keyBy('id');
            
            $skuIds = collect($data['items'])->pluck('product_sku_id')->unique();
            $skus = ProductSku::whereIn('id', $skuIds)->get()->keyBy('id');
            
            $subtotal = 0;
            $subDiscount = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $sku =  $skus->get($item['product_sku_id']);

                if (! $sku) {
                    throw new NotFoundSKUException();
                }
                
                $quantity = (int) $item['quantity'];

                $price = (int) ($item['price'] ?? (int) $sku->selling_price);
                $ratio = $units->get($item['measure_unit_id'])->ratio;
                $discount = (float) $item['discount'] ?? 0;
                
                $itemSubtotal = ($quantity * $ratio * $price) - $discount;

                $subtotal += $itemSubtotal;
                $subDiscount += $item['discount'];

                $stockLocationId = StockLocation::where('branch_id', $data['branch_id'])
                    ->whereHas('stocks', function ($query) use ($sku) {
                        $query->where('product_sku_id', $sku->id);
                    })->value('id');                
                
                $items[] = [
                    'product_sku_id' => $sku->id,
                    'quantity' => $quantity,
                    'measure_unit_id' => $item['measure_unit_id'],
                    'stock_location_id' => $stockLocationId,
                    'price' => $price,
                    'discount' => $discount,
                    'subtotal' => $item['subtotal'] == $itemSubtotal ? $item['subtotal'] : $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                ];
            }

            $saleDiscount = $data['discount'] ?? 0;
            $saleTax = $subtotal * (($data['tax_applied'] ?? false) ? 0.11 : 0);

            $grandTotal = $subtotal - $saleDiscount + $saleTax;

            $sale = Sale::create([
                'code' => $this->numberingService->generate(DocumentPrefix::SALE),
                'sales_channel_id' => $data['sales_channel_id'] ?? 1,
                'customer_id' => $data['customer_id'] ?? null,
                'branch_id' => $data['branch_id'],
                'subtotal' => $subtotal,
                'discount' => $saleDiscount,
                'tax' => $saleTax,
                'grand_total' => $grandTotal,
                'status' => SaleStatus::PENDING,
            ]);

            $branch = Branch::findOrFail($data['branch_id']);

            foreach ($items as $item) {
                $sale->items()->create($item);

                $sku = $skus->get($item['product_sku_id']);
                
                $this->stockService->reserve(
                    sku: $sku,
                    locationId: $item['stock_location_id'],
                    quantity: $item['quantity'],
                    reference: $sale,
                    notes: "Sale #{$sale->code}",
                );
            }

            return $sale->fresh([
                'items',
                'customer',
                'salesChannel',
                'payments.paymentMethod',
                'discounts',
            ]);
        });
    }

    public function pay(Sale $sale, array $data): PaymentData
    {
        return DB::transaction(function () use ($sale, $data) {
            $sale = Sale::lockForUpdate()->findOrFail($sale->id);

            if (! in_array($sale->status, [SaleStatus::PENDING, SaleStatus::PARTIAL])) {
                throw new CompletedOrExpiredSaleException();
            }

            $paidAmount = $sale->payments()?->sum('received_amount');

            $remainingAmount = $sale->grand_total - $paidAmount;

            $receivedAmount = $data['amount'];
            $data['received_amount'] = $receivedAmount;

            $paymentAmount = min($receivedAmount, $remainingAmount);
            
            $changeAmount = max(0, $receivedAmount - $remainingAmount);
            $data['change_amount'] = $changeAmount;

            $data['amount'] = $paymentAmount;

            $this->createPayment($sale, $data, $paidAmount);

            $sale->fresh([
                'items',
                'payments.paymentMethod',
                'customer',
                'salesChannel',
                'discounts',
            ]);

            return new PaymentData(
                sale: $sale,
                receivedAmount: $receivedAmount,
                paymentAmount: $paymentAmount,
                changeAmount: $changeAmount,
            );
        });
    }

    protected function createPayment(Sale $sale, array $data, int|float $paidAmount): void
    {
        $sale->payments()->create([
            'payment_method_id' => $data['payment_method_id'],
            'payment_amount' => $data['amount'],
            'received_amount' => $data['received_amount'],
            'change_amount' => $data['change_amount'],
            'reference' => $data['reference'] ?? null,
            'paid_at' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        $newAmount = $paidAmount + $data['amount'];

        $sale->update([
            'status' => $newAmount >= $sale->grand_total ? SaleStatus::PAID : SaleStatus::PARTIAL,
        ]);
    }

    public function complete(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            $completedSale = Sale::query()
            ->with([
                'items.sku',
                'branch',
                'payments',
            ])->lockForUpdate()
            ->findOrFail($sale->id);

            if ($completedSale->status !== SaleStatus::PAID) {
                throw new CompletedOrExpiredSaleException();
            }

            foreach ($completedSale->items as $item) {
                $this->stockService->finalizeReservation(
                    sku: $item->sku,
                    locationId: $item->stock_location_id,
                    quantity: $item->quantity,
                    reference: $completedSale,
                    notes: "Sale #{$completedSale->code}",
                );
            }

            $completedSale->update([
                'status' => SaleStatus::COMPLETED,
            ]);

            return $completedSale->fresh([
                'items.unit',
                'payments.paymentMethod',
                'customer',
                'salesChannel',
                'discounts',
            ]);
        });
    }

    public function cancel(Sale $sale): Sale
    {
        return DB::transaction(function () use ($sale) {
            $cancelledSale = Sale::query()
                ->with([
                    'items.sku',
                    'branch',
                ])
                ->lockForUpdate()
                ->findOrFail($sale->id);

            if (! in_array($cancelledSale->status, [SaleStatus::PENDING, SaleStatus::PARTIAL])) {
                throw new CannotCancelSaleException();
            }

            foreach ($cancelledSale->items as $item) {

                $this->stockService->release(
                    sku: $item->sku,
                    branch: $cancelledSale->branch,
                    quantity: $item->quantity,
                    reference: $cancelledSale,
                    notes: "Cancelled Sale #{$cancelledSale->code}",
                );
            }

            $cancelledSale->update([
                'status' => SaleStatus::CANCELLED,
            ]);

            return $cancelledSale->fresh([
                'items',
                'customer',
                'salesChannel',
                'payments.paymentMethod',
                'discounts',
            ]);
        });
    }
}