<?php

namespace App\Http\Requests\Promotion;

use App\Enums\PromotionDiscountType;
use App\Rules\DiscountValueMatchesType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromotionRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => [
                'required',
                Rule::enum(PromotionDiscountType::class)
            ],
            'discount_value' => [
                'required',
                'numeric',
                new DiscountValueMatchesType(PromotionDiscountType::from($this->discount_type)),
            ],
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ];
    }
}
