<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::all()->each(function (Product $p) {
            ProductImage::factory(3)->sequence(
                ['position' => 1],
                ['position' => 2],
                ['position' => 3],
            )->create([
                'product_id' => $p->id,
            ]);
        });
    }
}
