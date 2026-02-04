<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;
use Illuminate\Database\Seeder;

class MenuCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::all()->each(function (Menu $menu) {
            $menu->categories()->detach();

            Category::orderBy('position')->get()
                ->each(function (Category $category, int $index) use ($menu) {
                    $menu->categories()->attach($category->id, [
                        'position' => $index + 1,
                    ]);
                });
        });
    }
}
