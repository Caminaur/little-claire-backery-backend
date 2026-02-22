<?php

namespace App\Http\Requests\MenuProduct;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuProductRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'position' => 'required|integer|min:1',
            'custom_price' => 'nullable|numeric|min:0',
        ];
    }
}
