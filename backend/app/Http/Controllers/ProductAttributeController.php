<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductAttributeResource;
use App\Models\ProductAttribute;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::with('values:id,product_attribute_id,value,abbr')->get();
        return ProductAttributeResource::collection($attributes);
    }

    public function store(Request $request): ProductAttributeResource
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);
        
        $attribute = ProductAttribute::create($validated);
        return ProductAttributeResource::make($attribute);
    }

    public function update(Request $request, $id): ProductAttributeResource
    {        
        $validated = $request->validate([
            'name' => 'required|min:3|max:191|unique:product_attributes,name,' . $id,
            'is_active' => 'nullable|boolean',
            'description' => 'nullable',
            'values' => 'nullable|array',
            'values.*.id' => 'required_with:values',
            'values.*.value' => 'required_with:values|string|max:191',
            'values.*.abbr' => 'required_with:values|string|max:3',
        ]);

        return DB::transaction(function () use ($validated, $id) {
            $attribute = ProductAttribute::findOrFail($id);
            $attribute->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'is_active' => $validated['is_active'],
            ]);

            if (!empty($validated['values'])) {
                $submittedValues = collect($validated['values']);

                $existingIds = $submittedValues->pluck('id')->filter(fn ($val) => is_numeric($val))->toArray();

                $attribute->values()->whereNotIn('id', $existingIds)->delete();

                foreach ($submittedValues as $value) {
                    if (is_numeric($value['id'])) {
                        $attribute->values()
                            ->where('id', $value['id'])
                            ->update([
                                'value' => $value['value'],
                                'abbr' => $value['abbr'],
                        ]);
                    } else {
                        $attribute->values()->create([
                            'value' => $value['value'],
                            'abbr' => $value['abbr'],
                        ]);
                    }
                }
            }

            return ProductAttributeResource::make($attribute->fresh(['values']));
        });
    }

    public function destroy($id) {
        return response()->json([
            'success' => ProductAttribute::find($id)->delete(),
            'message' => 'Variant has been deleted successfully',
        ]);
    }
}
