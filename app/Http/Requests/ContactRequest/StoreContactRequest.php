<?php

namespace App\Http\Requests\ContactRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email',
            'phone'   => 'required|string',
            'message' => 'string|nullable',
        ];
    }
}
