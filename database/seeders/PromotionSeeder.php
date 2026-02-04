<?php

namespace Database\Seeders;

use App\Enums\PromotionDiscountType;
use App\Models\Promotion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Promotion::factory()->create([
            'discount_type' => PromotionDiscountType::Percentage,
            'discount_value' => 10,
        ]);

        Promotion::factory()->create([
            'discount_type' => PromotionDiscountType::Fixed,
            'discount_value' => 3.50,
        ]);
    }
}
