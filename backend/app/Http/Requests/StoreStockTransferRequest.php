<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_location_id' => [
                'required',
                'integer',
                'exists:stock_locations,id',
            ],

            'target_location_id' => [
                'required',
                'integer',
                'exists:stock_locations,id',
                'different:source_location_id',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:191',
            ],

            'auto' => [
                'nullable',
                'boolean',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.product_sku_id' => [
                'required',
                'integer',
                'exists:product_skus,id',
                'distinct',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
                'max:191',
            ],
        ];
    }
}