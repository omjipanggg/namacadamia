<?php
namespace App\Services\Purchase;

use App\Exceptions\PurchaseReceivedException;


use App\Enums\DocumentPrefix;
use App\Enums\PurchaseStatus;
use App\Enums\StockMovementType;

use App\Models\Purchase;
use App\Models\MeasureUnit;

use App\Services\Document\NumberingService;
use App\Services\Inventory\StockService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct(
        protected NumberingService $numberingService,
        protected StockService $stockService
    ) {}
    
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $purchase = Purchase::create([
                'code' => $this->numberingService->generate(DocumentPrefix::PURCHASE),
                'stock_location_id' => $data['stock_location_id'] ?? 1,
                'supplier_id' => $data['supplier_id'] ?? 1,
                'status' => PurchaseStatus::DRAFT,
                'notes' => $data['notes'] ?? null,
                'ordered_at' => $data['ordered_at'] ?? now(),
            ]);

            $grand_total = 0;
            foreach ($data['items'] as $item) {
                $purchase->items()->create([
                    'product_sku_id' => $item['product_sku_id'],
                    'measure_unit_id' => $item['measure_unit_id'],
                    'received_measure_unit_id' => $item['measure_unit_id'],
                    'quantity' => $item['quantity'],
                    'received_quantity' => 0,
                    'cost_price' => $item['cost_price'],
                    'subtotal' => $this->calculateSubtotal($item['quantity'], $item['measure_unit_id'], $item['cost_price']),
                    'notes' => $item['notes'] ?? null,
                ]);

                $grand_total += $this->calculateSubtotal($item['quantity'], $item['measure_unit_id'], $item['cost_price']);
            }

            if (!empty($data['auto_receive'])) {
                $this->performReceive($purchase);
            }

            $purchase->update(['grand_total' => $grand_total]);

            return $purchase->fresh([
                'location',
                'items.sku',
            ]);
        });
    }

    public function receive(Purchase $purchase)
    {
        return DB::transaction(fn () => $this->performReceive($purchase));
    }

    private function performReceive(Purchase $purchase): Purchase
    {
        if ($purchase->status === PurchaseStatus::COMPLETED) {
            throw new PurchaseReceivedException();
        }

        $purchase->load(['location', 'items.sku']);
                    
        foreach ($purchase->items as $item) {
            $remaining = $item->quantity - $item->received_quantity;

            if ($remaining <= 0) {
                continue;
            }

            $notes = "Purchase #{$purchase->code}";

            if ($item->notes) {
                $notes .= " - {$item->notes}";
            }

            $this->stockService->applyAdjustment(
                productSkuId: $item->sku->id,
                stockLocationId: $purchase->location->id,
                quantity: $remaining,
                type: StockMovementType::PURCHASE,
                reference: $purchase,
                notes: $notes,
            );

            $item->update([
                'received_quantity' => $item->received_quantity + $remaining,
            ]);
        }

        $purchase->update([
            'status' => PurchaseStatus::COMPLETED,
            'received_at' => now(),
        ]);

        return $purchase;
    }

    public function cancel(Purchase $purchase)
    {
        //
    }

    protected function calculateSubtotal(int $quantity, int $unitId, int $costPrice): int
    {
        $ratio = MeasureUnit::find($unitId)->ratio;
        return $quantity * $ratio * $costPrice;
    }

    public function paginate(Request $request): LengthAwarePaginator
    {
        $purchases = Purchase::query();

        if ($search = $request->search ?? null) {
            $purchases->where(function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
                }
            );
        }

        if ($status = $request->status ?? null) {
            $purchases->where('status', $status);
        }

        if ($request->sort_by) {
            $purchases->orderBy($request->sort_by, $request->sort_direction ?? 'asc');
        } else {
            $purchases->latest();
        }

        return $purchases->with([
            'supplier',
            'location',
            'items',
        ])->withSum('items', 'quantity')->paginate($request->per_page ?? 12)
        ->onEachSide(1);
    }
}