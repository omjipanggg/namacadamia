<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'customer_id' => ['nullable', 'exists:customers,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'sales_channel_id' => [
                'required',
                'exists:sales_channels,id'
            ],
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'tax_applied' => [
                'nullable',
                'boolean',
            ],
            'ordered_at' => ['nullable'],
            'notes' => [
                'nullable',
                'string',
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_sku_id' => [
                'required',
                'exists:product_skus,id'
            ],
            'items.*.quantity' => [
                'required',
                'min:1'
            ],
            'items.*.measure_unit_id' => [
                'required',
                'exists:measure_units,id'
            ],
            'items.*.discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'items.*.subtotal' => [
                'nullable',
                'numeric',
                'min:0',
            ],
            'items.*.notes' => [
                'nullable',
                'string'
            ],
        ];
    }
}
