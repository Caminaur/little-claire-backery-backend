<?php

namespace App\Http\Requests\ProductImage;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image'    => 'required|image|max:4096',
            'position' => 'required|integer|min:1',
        ];
    }
}
