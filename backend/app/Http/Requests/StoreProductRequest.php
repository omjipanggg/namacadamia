<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:191', 'unique:products,code'],
            'name' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],

            'categories' => ['required', 'array'],
            'categories.*' => [
                'integer',
                'exists:product_categories,id'
            ],

            'has_variants' => ['nullable', 'boolean'],

            'skus' => ['nullable', 'array'],
            'skus.*.id' => ['prohibited'],
            'skus.*.code' => ['required', 'string', 'max:191'],
            'skus.*.barcode' => ['nullable', 'string', 'max:191'],
            'skus.*.cost_price' => ['required', 'numeric', 'min:0'],
            'skus.*.selling_price' => ['required', 'numeric', 'min:0'],
            'skus.*.description' => ['nullable', 'string'],

            'skus.*.attributes' => ['nullable', 'array'],
            'skus.*.attributes.*' => [
                'integer',
                'exists:product_attribute_values,id'
            ],
        ];
    }
}
