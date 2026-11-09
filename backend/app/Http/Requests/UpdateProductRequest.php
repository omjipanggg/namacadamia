<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        $product = $this->route('product');

        return [
            'code' => [
                'required',
                'string',
                'max:191',
                Rule::unique('products', 'code')->ignore($product),
            ],

            'name' => ['required', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],

            'categories' => ['required', 'array'],
            'categories.*' => [
                'integer',
                'exists:product_categories,id',
            ],

            'has_variants' => ['nullable', 'boolean'],
            'skus' => ['nullable', 'array'],

            'skus.*.id' => [
                'nullable',
                'integer',
                'distinct',
            ],
            
            'skus.*.code' => [
                'nullable',
                'string',
                'max:191',
            ],
            'skus.*.barcode' => ['nullable', 'string', 'max:191'],
            'skus.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.selling_price' => ['nullable', 'numeric', 'min:0'],
            'skus.*.description' => ['nullable', 'string'],

            'skus.*.attributes' => ['nullable', 'array'],
            'skus.*.attributes.*' => [
                'nullable',
                'integer',
                'exists:product_attribute_values,id',
            ],
        ];
    }
}
