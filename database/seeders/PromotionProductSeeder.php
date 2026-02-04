<?php

namespace Database\Seeders;

use App\Models\Promotion;
use App\Models\Product;
use Illuminate\Database\Seeder;

class PromotionProductSeeder extends Seeder
{
    public function run(): void
    {
        $promotion = Promotion::where('is_active', true)->first();

        Product::inRandomOrder()->take(3)->get()->each(function (Product $product) use ($promotion) {
            $promotion->products()->attach($product->id);
        });
    }
}
