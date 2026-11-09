<?php
namespace App\Services\Product;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductAttributeValue;
use App\Models\Stock;
use App\Models\StockLocation;

use Illuminate\Http\Request;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function paginate(Request $request): LengthAwarePaginator
    {
        return Product::query()
            ->withCount('skus')
            ->search($request->search)
            ->whereHasCategory($request->category)
            ->sort($request->sort_by, $request->sort_direction)
            ->paginate($request->integer('per_page', 12))
            ->onEachSide(1);
    }
    
    public function store(array $data): Product
    {
        return DB::transaction(function ($query) use ($data) {
            $product = $this->createProduct($data);

            $this->syncCategories($product, $data);

            if ($data['has_variants'] ?? false) {
                $this->createVariantSkus($product, $data ?? []);
            } else {
                $this->createSimpleSku($product, $data);
            }

            return $product->fresh([
                'categories',
                'skus.variants.productAttribute',
                'skus.media',
                'skus.stocks',
            ]);
        });
    }
    
    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function ($query) use ($product, $data) {
            $product->update([
                'code' => $data['code'],
                'name' => $data['name'],
                'price' => $data['price'],
                'description' => $data['description'],
            ]);
            
            $this->syncCategories($product, $data);

            if ($data['has_variants'] ?? false) {
                $this->syncVariantSkus($product, $data['skus'] ?? []);
            } else {
                $this->syncSimpleSku($product, $data);
            }

            return $product->fresh([
                'categories',
                'skus.variants.productAttribute',
                'skus.media',
                'skus.stocks',
            ]);
        });
    }

    public function destroy(Product $product): void
    {

    }

    private function createProduct(array $data): Product
    {
        return Product::create([
            'code' => $data['code'],
            'name' => $data['name'],
            'price' => $data['price'],
            'description' => $data['description'],
        ]);
    }

    private function syncCategories(Product $product, array $data): void
    {
        $product->categories()->sync($data['categories'] ?? []);
    }

    private function createSimpleSku(Product $product, array $data): void
    {
        $sku = $product->skus()->create([
            'code' => $this->generateSku($product->code),
            'barcode' => mt_rand(1000000000000000, 9999999999999999),
            'cost_price' => $data['cost_price'] ?? $data['price'],
            'selling_price' => $data['selling_price'] ?? $data['price'],
            'description' => $data['description'] ?? null,
        ]);

        $this->initStock($sku);
    }

    private function syncSimpleSku(Product $product, array $data): void
    {
        $sku = $product->skus()->first();

        if ($sku) {
            $sku->update([
                'cost_price' => $data['cost_price'] ?? $data['price'],
                'selling_price' => $data['selling_price'] ?? $data['price'],
                'description' => $data['description'] ?? null,
            ]);

            $sku->variants()->detach();

            return;
        }

        $this->createSimpleSku($product, $data);
    }
    
    private function createVariantSkus(Product $product, array $data): void
    {
        foreach ($data['skus'] as $skuData) {
            $sku = $product->skus()->create([
                'code' => $skuData['code'], // currently being handled by the frontend
                'barcode' => $skuData['barcode'] ?? null,
                'cost_price' => $skuData['cost_price'] ?? 0,
                'selling_price' => $skuData['selling_price'] ?? $product->price,
                'description' => $skuData['description'] ?? null,
            ]);

            $sku->variants()->sync($skuData['attributes']);

            $this->initStock($sku);
        }
    }

    private function syncVariantSkus(Product $product, array $skus): void
    {
        foreach ($skus as $skuData) {
            if (!empty($skuData['id'])) {
                $sku = $product->skus()
                    ->whereKey($skuData['id'])
                    ->firstOrFail();

                $sku->update([
                    'code' => $skuData['code'],
                    'barcode' => $skuData['barcode'] ?? null,
                    'cost_price' => $skuData['cost_price'] ?? 0,
                    'selling_price' => $skuData['selling_price'] ?? $product->price,
                    'description' => $skuData['description'] ?? null,
                ]);
            } else {
                $sku = $product->skus()->create([
                    'code' => $skuData['code'],
                    'barcode' => $skuData['barcode'] ?? null,
                    'cost_price' => $skuData['cost_price'] ?? 0,
                    'selling_price' => $skuData['selling_price'] ?? $product->price,
                    'description' => $skuData['description'] ?? null,
                ]);

                $this->initStock($sku);
            }

            $sku->variants()->sync(
                $skuData['attributes'] ?? []
            );
        }
    }
    
    private function generateSku(string $code): string
    {
        $base = Str::upper($code);
        $sequence = ProductSku::where('code', 'LIKE', "{$base}-%")->count() + 1;

        return "{$base}-" . Str::padLeft($sequence, 4, '0');
    }

    private function initStock(ProductSku $sku): void
    {
        $main = StockLocation::query()
            ->where([
                'type' => 'warehouse',
                'is_active' => true,
            ])->firstOrFail();

        $sku->stocks()->create([
            'stock_location_id' => $main->id,
            'quantity' => 0,
        ]);
    }

    private function initializeStocks(ProductSku $sku): void
    {
        StockLocation::query()
            ->select('id')
            ->each(function (StockLocation $location) use ($sku) {
                $sku->stocks()->create([
                    'stock_location_id' => $location->id,
                    'quantity' => 0,
                ]);
            });
    }

    private function uploadMedia(ProductSku $sku, array $files): void
    {
        // sooner
    }

    public function like(Product $product): void
    {
        $product->increment('likes_count');
    }
}