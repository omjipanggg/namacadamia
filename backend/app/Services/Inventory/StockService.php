<?php
namespace App\Services\Inventory;

use App\Exceptions\CannotReleaseStockException;
use App\Exceptions\InsufficientStockException;

use App\Enums\StockMovementType;

use App\Models\Branch;
use App\Models\ProductSku;
use App\Models\Stock;
use App\Models\StockLocation;
use App\Models\StockMovement;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService {
    public function adjust(int $productSkuId, int $stockLocationId, int $quantity, StockMovementType $type = StockMovementType::ADJUSTMENT, $reference = null, ?string $notes = null): ?Stock
    {
        return DB::transaction(function () use (
            $productSkuId,
            $stockLocationId,
            $quantity,
            $type,
            $reference,
            $notes,
        ) {        
            return $this->applyAdjustment(
                productSkuId: $productSkuId,
                stockLocationId: $stockLocationId,
                quantity: $quantity,
                type: $type,
                reference: $reference,
                notes: $notes,
            );
        });
    }

    public function reserve(ProductSku $sku, int $locationId, int $quantity, Model $reference, ?string $notes = null): ?Stock
    {
        if (! $sku->track_stock) {
            return null;
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }
        
        return DB::transaction(function () use (
            $sku,
            $locationId,
            $quantity,
            $reference,
            $notes,
        ) {
            $stock = Stock::query()
                ->where('stock_location_id', $locationId)
                ->where('product_sku_id', $sku->id)
                ->lockForUpdate()
                ->first();

            if (! $stock) {
                if (! $sku->allow_backorder) {
                    throw new InsufficientStockException();
                }

                $stock = Stock::firstOrCreate([
                    'product_sku_id' => $sku->id,
                    'stock_location_id' => $locationId,
                ]);
            }

            if (($stock->quantity - $stock->quantity_reserved) < $quantity) {
                if ($sku->allow_backorder) {
                    return $stock;
                }
                throw new InsufficientStockException();
            }

            $before = $stock->quantity_reserved;
            $stock->increment('quantity_reserved', $quantity);
            
            $stock->refresh();

            $this->createMovement(
                $stock,
                $before,
                0,
                $stock->quantity_reserved,
                StockMovementType::RESERVE,
                $reference,
                $notes
            );

            return $stock;
        });
    }
    
    public function release(ProductSku $sku, int $locationId, int $quantity, Model $reference, ?string $notes = null): ?Stock
    {
        if (! $sku->track_stock) {
            return null;
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }

        return DB::transaction(function () use ($sku, $locationId, $quantity, $reference, $notes) {
            $stock = Stock::query()
                ->where('stock_location_id', $locationId)
                ->where('product_sku_id', $sku->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stock->quantity_reserved < $quantity) {
                throw new CannotReleaseStockException();
            }

            $before = $stock->quantity_reserved;
            $stock->decrement('quantity_reserved', $quantity);

            $stock->refresh();

            $this->createMovement(
                $stock,
                $before,
                0,
                $stock->quantity_reserved,
                StockMovementType::RELEASE,
                $reference,
                $notes,
            );
            
            return $stock;
        });
    }

    protected function createMovement(Stock $stock, int $before, int $quantity, int $after, StockMovementType $type, Model $reference, ?string $notes = null): StockMovement
    {
        return $stock->movements()->create([
            'reference_type' => $reference?->getMorphClass(),
            'reference_id' => $reference?->getKey(),
            'type' => $type,
            'quantity_before' => $before,
            'quantity' => $quantity,
            'quantity_after' => $after,
            'notes' => $notes,
        ]);
    }
    
    public function setQuantity(int $productSkuId, int $stockLocationId, int $quantity, StockMovementType $type = StockMovementType::ADJUSTMENT, $reference = null, ?string $notes = null): ?Stock
    {
        return DB::transaction(function () use(
            $productSkuId,
            $stockLocationId,
            $quantity,
            $type,
            $reference,
            $notes,
        ) {
            $stock = Stock::query()
                ->where('stock_location_id', $stockLocationId)
                ->where('product_sku_id', $productSkuId)
                ->first();

            $currentQuantity = $stock?->quantity ?? 0;
            $difference = $quantity - $currentQuantity;

            if ($difference === 0) {
                return $stock;
            }

            return $this->applyAdjustment(
                productSkuId: $productSkuId,
                stockLocationId: $stockLocationId,
                quantity: $difference,
                type: $type,
                reference: $reference,
                notes: $notes,
            );
        });
    }

    public function finalizeReservation(ProductSku $sku, int $locationId, int $quantity, Model $reference, ?string $notes = null): Stock
    {
        return DB::transaction(function () use (
            $sku,
            $locationId,
            $quantity,
            $reference,
            $notes,
        ) {
            $stock = Stock::where([
                'stock_location_id' => $locationId,
                'product_sku_id' => $sku->id,
            ])->lockForUpdate()->firstOrFail();

            if ($stock->quantity_reserved < $quantity) {
                throw new CannotReleaseStockException();
            }

            if ($stock->quantity < $quantity) {
                throw new InsufficientStockException();
            }

            $before = $stock->quantity;
            $stock->decrement('quantity', $quantity);
            $stock->decrement('quantity_reserved', $quantity);

            $stock->refresh();

            $this->createMovement(
                $stock,
                $before,
                $quantity,
                $stock->quantity,
                StockMovementType::SALE,
                $reference,
                $notes
            );
            
            return $stock;
        });
    }

    public function applyAdjustment(int $productSkuId, int $stockLocationId, int $quantity, StockMovementType $type, $reference = null, ?string $notes): ?Stock
    {
        $stock = Stock::query()
            ->where('stock_location_id', $stockLocationId)
            ->where('product_sku_id', $productSkuId)
            ->lockForUpdate()
            ->first();
        
        if (!$stock) {                
            $stock = Stock::firstOrCreate([
                'stock_location_id' => $stockLocationId,
                'product_sku_id' => $productSkuId,
            ], [
                'quantity' => 0,
                'quantity_reserved' => 0,
                'quantity_minimum' => 1,
                'quantity_maximum' => 999,
            ]);
        }

        $before = $stock->quantity;
        $after = $before + $quantity;

        if ($after < 0) {
            throw ValidationException::withMessage([
                'negative_quantity' => 'Stock cannot be a negative value.',
                'insufficient_stock' => "Insufficient stock for SKU {$productSkuId}.",
            ]);
        }

        $stock->update([
            'quantity' => $after,
        ]);
        
        $this->createMovement(
            $stock,
            $before,
            $quantity,
            $after,
            $type,
            $reference ?? $stock,
            $notes
        );

        return $stock->fresh();
    }
    
    public function paginate(Request $request): LengthAwarePaginator
    {
        $stocks = Stock::query();

        if ($search = $request->search ?? null) {
            $stocks->where(function ($query) use ($search) {
                $query->whereHas('location', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                })->orWhereHas('sku', function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%");
                });
            });
        }

        if ($request->sort_by) {
            $stocks->orderBy($request->sort_by, $request->sort_direction ?? 'asc');
        } else {
            $stocks->latest();
        }

        return $stocks->with(['sku', 'location'])->paginate($request->per_page ?? 12)->onEachSide(1);
    }

    /*
    private function hasAvailableStock(int $productSkuId, int $stockLocationId, int $quantity): bool
    {
        $stock = Stock::where([
            'stock_location_id' => $stockLocationId,
            'product_sku_id' => $productSkuId,
        ])->first();

        return $stock && ($stock->quantity - $stock->quantity_reserved) >= $quantity;
    }
    */
}