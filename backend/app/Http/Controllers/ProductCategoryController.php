<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Http\Resources\ProductCategoryResource;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = ProductCategory::query()->withCount('products');
        return ProductCategoryResource::collection($this->paginate($request, $categories, ['name'], 'name'));
    }
    
    /**
     * Store a newly created resource in storage.
    */    
    public function store(Request $request)
    {
        $category = ProductCategory::create($this->validateRequest($request));
        return ProductCategoryResource::make($category);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $category)
    {
        return ProductCategoryResource::make($category->load(['products']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductCategory $category)
    {
        $category->update($this->validateRequest($request));
        return ProductCategoryResource::make($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $category)
    {
        return response()->json([
            'success' => $category->delete(),
            'message' => 'Category has been deleted successfully',
        ]);
    }

    private function validateRequest(Request $request) {
        return $request->validate([
            'name' => 'required|string|min:3|max:191',
            'is_active' => 'nullable|boolean'
        ]);
    }
}