<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PaySaleRequest extends FormRequest
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
            'payment_method_id' => [
                'required',
                'exists:payment_methods,id'
            ],
            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'reference' => [
                'nullable',
                'string',
                'max:191',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}
