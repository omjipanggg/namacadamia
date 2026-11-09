<?php

namespace App\Http\Controllers;

use App\Models\ProductAttributeValue;
use App\Http\Resources\ProductAttributeValueResource;

use Illuminate\Http\Request;

class ProductAttributeValueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $values = ProductAttributeValue::orderBy('product_attribute_id')->get();
        return ProductAttributeValueResource::collection($values);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): ProductAttributeValueResource
    {
        $validated = $request->validate([
            'name' => 'required|unique',
            'abbr' => 'required|unique',
        ]);

        $value = ProductAttributeValue::create($validated);
        return ProductAttributeValueResource::make($value);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductAttributeValue $productAttributeValue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductAttributeValue $productAttributeValue)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductAttributeValue $productAttributeValue)
    {
        //
    }
}
