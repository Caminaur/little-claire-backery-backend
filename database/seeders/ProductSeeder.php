<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    // Variant sets used at random to simulate real-world products:
    // null = single unnamed variant (simple price, like a fixed-price item)
    private array $variantSets = [
        null,
        null,
        [['label' => 'Chico', 'price_range' => [3, 6]], ['label' => 'Mediano', 'price_range' => [5, 9]], ['label' => 'Grande', 'price_range' => [7, 12]]],
        [['label' => '250ml', 'price_range' => [3, 5]], ['label' => '350ml', 'price_range' => [5, 8]], ['label' => '500ml', 'price_range' => [7, 11]]],
        [['label' => 'Chico', 'price_range' => [4, 7]], ['label' => 'Grande', 'price_range' => [7, 13]]],
    ];

    public function run(): void
    {
        Category::all()->each(function (Category $c) {
            Product::factory(20)
                ->create(['category_id' => $c->id])
                ->each(function (Product $product) {
                    $set = $this->variantSets[array_rand($this->variantSets)];

                    if ($set === null) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'label' => null,
                            'price' => fake()->randomFloat(2, 2, 15),
                            'position' => 1,
                            'is_active' => true,
                        ]);
                    } else {
                        foreach ($set as $i => $variant) {
                            ProductVariant::create([
                                'product_id' => $product->id,
                                'label' => $variant['label'],
                                'price' => fake()->randomFloat(2, ...$variant['price_range']),
                                'position' => $i + 1,
                                'is_active' => true,
                            ]);
                        }
                    }
                });
        });
    }
}
