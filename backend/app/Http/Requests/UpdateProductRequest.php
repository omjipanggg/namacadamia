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
                'distinct',
            ],

            'has_variants' => ['nullable', 'boolean'],

            'attributes' => ['required_if:has_variants,true', 'array'],
            'attributes.*.id' => [
                'required',
                'integer',
                'exists:product_attributes,id',
            ],
            'attributes.*.label' => ['required', 'string', 'max:255'],
            'attributes.*.values' => ['required', 'array', 'min:1'],
            'attributes.*.values.*.id' => ['nullable', 'integer'],
            'attributes.*.values.*.label' => [
                'required',
                'string',
                'max:255',
            ],

            'skus' => ['required_if:has_variants,true', 'array', 'min:1'],
            
            'skus.*.id' => [
                'nullable',
                'integer',
                Rule::exists('product_skus', 'id')->where('product_id', $product->id),
            ],
            'skus.*.code' => ['prohibited'],

            'skus.*.barcode' => ['nullable', 'string', 'max:191'],
            'skus.*.cost_price' => ['required', 'numeric', 'min:0'],
            'skus.*.selling_price' => ['required', 'numeric', 'min:0'],
            'skus.*.description' => ['nullable', 'string'],
            'skus.*.is_active' => ['nullable', 'boolean'],

            'skus.*.attributes' => ['required', 'array', 'min:1'],
            'skus.*.attributes.*' => ['required', 'array'],
            'skus.*.attributes.*.id' => ['nullable', 'integer'],
            'skus.*.attributes.*.label' => [
                'required',
                'string',
                'max:255',
            ],
            'skus.*.attributes.*.attribute_id' => [
                'required',
                'integer',
                'exists:product_attributes,id',
            ],
        ];
    }

}
