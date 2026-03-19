<?php

namespace App\Http\Requests\MenuProduct;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // custom_price was removed in favour of product_variants.price
    public function rules(): array
    {
        return [];
    }
}
