<?php

namespace App\Http\Controllers\Panel;

use App\Models\Product;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductIndexResource;
use App\Http\Resources\ProductDetailResource;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

use App\Services\Product\ProductService;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    public function __construct(protected ProductService $service) {}
    public function index(Request $request)
    {
        $products = $this->service->paginate($request);
        return ProductIndexResource::collection($products);
    }

    public function show(Product $product) {
        $product->load([
            'categories',
            'skus.media',
            'skus.stocks',
            'skus.variants.productAttribute',
        ]);
        return ProductDetailResource::make($product);
    }

    public function store(StoreProductRequest $request) {
        $product = $this->service->store($request->validated());
        return ProductIndexResource::make($product);
    }

    public function update(UpdateProductRequest $request, Product $product) {        
        $updatedProduct = $this->service->update($product, $request->validated());
        return ProductIndexResource::make($updatedProduct);
    }

    public function destroy(Product $product) {
        return $this->service->destroy($product);
    }
}
