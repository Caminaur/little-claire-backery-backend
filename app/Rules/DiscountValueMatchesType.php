<?php

namespace App\Rules;

use App\Enums\PromotionDiscountType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DiscountValueMatchesType implements ValidationRule
{
    private PromotionDiscountType $type;
    public function __construct(PromotionDiscountType $type)
    {
        $this->type = $type;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->type === PromotionDiscountType::Percentage && ($value < 1 || $value > 100)) {
            $fail('Percentage discounts must be between 1 and 100.');
        }

        if ($this->type === PromotionDiscountType::Fixed && $value <= 0) {
            $fail('Fixed discounts must be greater than 0.');
        }
    }
}
