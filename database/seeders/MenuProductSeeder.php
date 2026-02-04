<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MenuProductSeeder extends Seeder
{
    public function run(): void
    {
        Menu::all()->each(function (Menu $menu) {
            $menu->products()->detach();

            $position = 1;

            Product::inRandomOrder()
                ->take(10)
                ->get()
                ->each(function (Product $product) use ($menu, &$position) {
                    $menu->products()->attach($product->id, [
                        'position' => $position++,
                    ]);
                });
        });
    }
}
