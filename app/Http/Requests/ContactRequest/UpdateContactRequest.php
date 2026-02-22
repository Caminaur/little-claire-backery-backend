<?php

namespace App\Http\Requests\ContactRequest;

use App\Enums\ContactRequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
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
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|string|email',
            'phone' => 'sometimes|string',
            'message' => 'sometimes|string|nullable',
            'type' => [
                'sometimes',
                'required',
                'string',
                Rule::enum(ContactRequestType::class)
            ],
            'is_read' => 'sometimes|boolean',
        ];
    }
}
