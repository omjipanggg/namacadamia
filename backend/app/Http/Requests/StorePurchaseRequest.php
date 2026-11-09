<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'stock_location_id' => ['required', 'exists:stock_locations,id'],
            'notes' => ['nullable', 'string'],
            'ordered_at' => ['nullable'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_sku_id' => [
                'required',
                'exists:product_skus,id',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:1',
            ],
            'items.*.measure_unit_id' => [
                'required',
                'exists:measure_units,id',
            ],
            'items.*.cost_price' => [
                'required',
                'numeric',
                'min:0',
            ],
            'items.*.notes' => [
                'nullable',
                'string',
            ],
            'auto_receive' => ['nullable', 'boolean'],
        ];
    }
}
