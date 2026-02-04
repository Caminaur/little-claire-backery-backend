<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::all()->each(function (Category $c) {
            Product::factory(20)
                ->create(['category_id' => $c->id]);
        });
    }
}
