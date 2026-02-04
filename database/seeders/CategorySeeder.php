<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()
            ->count(5)
            ->sequence(
                ['position' => 1],
                ['position' => 2],
                ['position' => 3],
                ['position' => 4],
                ['position' => 5]
            )->create();
    }
}
