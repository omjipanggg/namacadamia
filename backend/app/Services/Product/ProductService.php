<?php
namespace App\Services\Product;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Stock;
use App\Models\StockLocation;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

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
                $this->syncVariantSkus($product, $data['attributes'] ?? [], $data['skus'] ?? []);
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
                $this->syncVariantSkus($product, $data['attributes'] ?? [], $data['skus'] ?? []);
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

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
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

    /**
     * Synchronize product attribute values and SKU combinations.
     */
    private function syncVariantSkus(
        Product $product,
        array $attributes,
        array $skus
    ): void {
        // Resolve and persist attribute values first.
        $resolvedValues = $this->resolveAttributeValues($attributes);

        // Include soft-deleted SKUs so existing combinations can be restored.
        $existingSkus = $product->skus()
            ->withTrashed()
            ->with('variants')
            ->get();

        // Index existing SKUs by their attribute combinations.
        $existingByCombination = $existingSkus
            ->filter(fn ($sku) => $sku->variants->isNotEmpty())
            ->keyBy(fn ($sku) => $this->combinationKey(
                $sku->variants->pluck('id')->all()
            ));

        $activeSkuIds = [];
        $processedCombinations = [];

        foreach ($skus as $skuData) {
            // Resolve frontend attribute values into actual database IDs.
            $attributeValues = collect(
                $skuData['attributes'] ?? []
            )->map(function ($value) use ($resolvedValues) {
                $identity = $this->attributeValueIdentity($value);

                $attributeValue = $resolvedValues[$identity] ?? null;

                if (!$attributeValue) {
                    throw ValidationException::withMessages([
                        'skus' => "Unable to resolve attribute value: {$value['label']}.",
                    ]);
                }

                return $attributeValue;
            });

            $valueIds = $attributeValues->pluck('id')->all();

            if (empty($valueIds)) {
                throw ValidationException::withMessages([
                    'skus' => 'Each SKU must have at least one attribute value.',
                ]);
            }

            $combinationKey = $this->combinationKey($valueIds);

            // Prevent duplicate combinations in the same request.
            if (isset($processedCombinations[$combinationKey])) {
                throw ValidationException::withMessages([
                    'skus' => 'Duplicate SKU combinations are not allowed.',
                ]);
            }

            $processedCombinations[$combinationKey] = true;

            // Find an existing SKU with the same combination.
            $sku = $existingByCombination->get($combinationKey);

            $isNew = !$sku;

            if ($isNew) {
                $sku = $product->skus()->make();

                $sku->code = $this->generateVariantSkuCode(
                    $product,
                    $attributeValues
                );
            } else {
                // Restore a previously soft-deleted SKU if necessary.
                if ($sku->trashed()) {
                    $sku->restore();
                }
            }

            $sku->fill([
                'barcode' => $skuData['barcode'] ?? $sku->barcode,
                'cost_price' => $skuData['cost_price'] ?? 0,
                'selling_price' => $skuData['selling_price'] ?? $product->price,
                'description' => $skuData['description'] ?? null,
                'is_active' => true,
            ]);

            $sku->save();

            // Synchronize actual ProductAttributeValue IDs.
            $sku->variants()->sync($valueIds);

            // Initialize stock only for newly created SKUs.
            if ($isNew) {
                $this->initStock($sku);
            }

            $activeSkuIds[] = $sku->id;
        }

        // Deactivate combinations that are no longer selected.
        $product->skus()
            ->withTrashed()
            ->whereNotIn('id', $activeSkuIds ?: [0])
            ->update([
                'is_active' => false,
            ]);
    }

    /**
     * Create or restore attribute values and resolve their identities.
     */
    private function resolveAttributeValues(array $groups): array
    {
        $resolved = [];

        foreach ($groups as $group) {
            $attributeId = $group['id'] ?? null;

            if (!$attributeId) {
                throw ValidationException::withMessages([
                    'attributes' => 'Invalid product attribute.',
                ]);
            }

            $attribute = ProductAttribute::find($attributeId);

            if (!$attribute) {
                throw ValidationException::withMessages([
                    'attributes' => "Attribute {$attributeId} does not exist.",
                ]);
            }

            foreach ($group['values'] ?? [] as $value) {
                $label = trim($value['label'] ?? '');

                if ($label === '') {
                    throw ValidationException::withMessages([
                        'attributes' => 'Attribute values cannot be empty.',
                    ]);
                }

                if (!empty($value['id'])) {
                    // Existing value: verify it belongs to this attribute.
                    $attributeValue = ProductAttributeValue::withTrashed()
                        ->where('product_attribute_id', $attributeId)
                        ->find($value['id']);

                    if (!$attributeValue) {
                        throw ValidationException::withMessages([
                            'attributes' => "Invalid attribute value ID: {$value['id']}.",
                        ]);
                    }
                } else {
                    // New value: check whether it already exists.
                    $attributeValue = ProductAttributeValue::withTrashed()
                        ->where('product_attribute_id', $attributeId)
                        ->where('value', $label)
                        ->first();

                    if (!$attributeValue) {
                        $attributeValue = ProductAttributeValue::create([
                            'product_attribute_id' => $attributeId,
                            'value' => $label,
                            'abbr' => $this->generateUniqueAbbr($attributeId),
                            'sort_order' => 0,
                            'is_active' => true,
                        ]);
                    }
                }

                if ($attributeValue->trashed()) {
                    $attributeValue->restore();
                }

                $identity = $this->attributeValueIdentity([
                    'id' => $value['id'] ?? null,
                    'attribute_id' => $attributeId,
                    'label' => $label,
                ]);

                $resolved[$identity] = $attributeValue;
            }
        }

        return $resolved;
    }

    /**
     * Generate a unique three-character abbreviation per attribute.
     */
    private function generateUniqueAbbr(int $attributeId): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        do {
            $abbr = '';

            for ($i = 0; $i < 3; $i++) {
                $abbr .= $characters[random_int(
                    0,
                    strlen($characters) - 1
                )];
            }

        } while (
            ProductAttributeValue::withTrashed()
                ->where('product_attribute_id', $attributeId)
                ->where('abbr', $abbr)
                ->exists()
        );

        return $abbr;
    }

    /**
     * Generate a SKU code from resolved database abbreviations.
     */
    private function generateVariantSkuCode(
        Product $product,
        $attributeValues
    ): string {
        $suffix = $attributeValues
            ->pluck('abbr')
            ->implode('-');

        $base = Str::upper($product->code . '-' . $suffix);

        $code = $base;
        $sequence = 1;

        // Protect the globally unique SKU code constraint.
        while (
            ProductSku::withTrashed()
                ->where('code', $code)
                ->exists()
        ) {
            $code = $base . '-' . Str::padLeft(
                (string) $sequence,
                2,
                '0'
            );

            $sequence++;
        }

        return $code;
    }

    /**
     * Generate a stable identity for an attribute value.
     */
    private function attributeValueIdentity(array $value): string
    {
        if (!empty($value['id'])) {
            return 'id:' . $value['id'];
        }

        return 'new:' .
            ($value['attribute_id'] ?? '') . ':' .
            mb_strtolower(trim($value['label'] ?? ''));
    }

    /**
     * Generate a stable key for a SKU combination.
     */
    private function combinationKey(array $valueIds): string
    {
        $valueIds = array_map('intval', $valueIds);

        sort($valueIds);

        return implode('-', $valueIds);
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