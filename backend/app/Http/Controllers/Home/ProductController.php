<?php

namespace App\Http\Controllers\Home;

use App\Models\Product;
use App\Models\ProductCategory;

use App\Http\Controllers\Controller;

use App\Http\Resources\CatalogResource;
use App\Http\Resources\ProductCategoryResource;

use App\Services\Product\ProductService;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $data = Product::query()
            ->with(['skus'])
            ->search($request->search)
            ->whereHasCategory($request->category)
            ->sort($request->sort_by, $request->sort_direction)
            ->withCount('skus')
            ->paginate($request->integer('per_page', 12))
            ->onEachSide(1);

        return CatalogResource::collection($data);
    }

    public function categoryIndex()
    {
        $categories = ProductCategory::query()->orderBy('name')->withCount('products')->get();
        return ProductCategoryResource::collection($categories);
    }

    public function categoryShow(ProductCategory $category) {
        return ProductCategoryResource::make($category->load(['products']));
    }

    public function show(Product $product) {
        $product = $product->load(['skus']);
        return CatalogResource::make($product);
    }

    public function like(Product $product, ProductService $productService) {
        $productService->like($product);
        return response()->json(['message' => 'Product liked successfully']);
    }
}
