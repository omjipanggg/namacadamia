<?php
namespace App\Services\Inventory;

use App\Exceptions\InsufficientStockException;

use App\Enums\DocumentPrefix;
use App\Enums\StockMovementType;
use App\Enums\StockTransferStatus;

use App\Models\Stock;
use App\Models\StockLocation;
use App\Models\StockTransfer;

use App\Services\Document\NumberingService;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferService
{
    public function __construct(protected NumberingService $numberingService, protected StockService $stockService) {}

    public function create(StockLocation $source, StockLocation $target, array $items, ?string $notes = null, bool $auto = false): StockTransfer
    {
        $this->validateLocations($source, $target);

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Stock transfer must contain at least one item.',
            ]);
        }

        return DB::transaction(function () use ($source, $target, $items, $notes, $auto) {
            $transfer = StockTransfer::create([
                'code' => $this->numberingService->generate(DocumentPrefix::TRANSFER),
                'source_location_id' => $source->id,
                'target_location_id' => $target->id,
                'status' => StockTransferStatus::DRAFT,
                'notes' => $notes,
            ]);

            $transfer->items()->createMany($items);

            if ($auto) {
                return $this->performCompletion($transfer);
            }

            return $transfer->load([
                'sourceLocation',
                'targetLocation',
                'items.sku.product',
            ]);
        });
    }

    public function transfer(
        int $productSkuId,
        StockLocation $source,
        StockLocation $target,
        int $quantity,
        ?Model $reference = null,
        ?string $notes = null,
    ): array {
        $this->validateLocations($source, $target);
        
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Transfer quantity must be greater than zero.',
            ]);
        }

        return DB::transaction(fn () => $this->transferItem($productSkuId, $source, $target, $quantity, $reference, $notes));
    }

    private function performCompletion(StockTransfer $transfer): StockTransfer
    {
        $transfer = StockTransfer::query()
            ->with([
                'items',
                'sourceLocation',
                'targetLocation',
            ])
            ->lockForUpdate()
            ->findOrFail($transfer->id);

        if ($transfer->status !== StockTransferStatus::DRAFT) {
            throw ValidationException::withMessages([
                'status' => 'Only draft stock transfers can be completed.',
            ]);
        }

        foreach ($transfer->items as $item) {
            $transferredItems[] = $this->transferItem(
                productSkuId: $item->product_sku_id,
                source: $transfer->sourceLocation,
                target: $transfer->targetLocation,
                quantity: $item->quantity,
                notes: $item->notes ?? $transfer->notes,
                reference: $transfer,
            );
        }

        $transfer->update([
            'status' => StockTransferStatus::COMPLETED,
        ]);

        return $transfer->fresh([
            'sourceLocation',
            'targetLocation',
            'items.sku.product',
        ]);
    }

    public function complete(StockTransfer $transfer)
    {
        return DB::transaction(fn () => $this->performCompletion($transfer));
    }

    private function transferItem(int $productSkuId, StockLocation $source, StockLocation $target, int $quantity, ?Model $reference = null, ?string $notes = null): array
    {
        $sourceStock = Stock::query()
            ->where('stock_location_id', $source->id)
            ->where('product_sku_id', $productSkuId)
            ->lockForUpdate()
            ->first();

        if (!$sourceStock) {
            throw ValidationException::withMessages([
                'insufficient_at_source' => 'Insufficient stock records at the source location.',
            ]);
        }

        $availableQuantity = $sourceStock->quantity - $sourceStock->quantity_reserved;

        if ($availableQuantity < $quantity) {
            throw new InsufficientStockException();
        }

        $finalSource = $this->stockService->applyAdjustment(
            productSkuId: $productSkuId,
            stockLocationId: $source->id,
            quantity: -$quantity,
            type: StockMovementType::TRANSFER_OUT,
            reference: $reference,
            notes: $notes,
        );

        $finalTarget = $this->stockService->applyAdjustment(
            productSkuId: $productSkuId,
            stockLocationId: $target->id,
            quantity: $quantity,
            type: StockMovementType::TRANSFER_IN,
            reference: $reference,
            notes: $notes,
        );

        return [
            'source' => $finalSource,
            'target' => $finalTarget,
        ];
    }
    
    protected function validateLocations(
        StockLocation $source,
        StockLocation $target
    ): void {
        if ($source->is($target)) {
            throw ValidationException::withMessages([
                'locations' => 'Source and target stock locations must be different.',
            ]);
        }

        if (! $source->is_active) {
            throw ValidationException::withMessages([
                'source_location' => 'The source stock location is inactive.',
            ]);
        }

        if (! $target->is_active) {
            throw ValidationException::withMessages([
                'target_location' => 'The target stock location is inactive.',
            ]);
        }
    }
}