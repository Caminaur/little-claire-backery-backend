<?php

namespace App\Http\Requests\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'position' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ];
    }
}
