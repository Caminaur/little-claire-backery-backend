<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            // Datos de prueba (fake)
            // CategorySeeder::class,
            // ProductSeeder::class,
            // ProductImageSeeder::class,
            // MenuSeeder::class,
            // MenuCategorySeeder::class,
            // MenuProductSeeder::class,
            // PromotionSeeder::class,
            // PromotionProductSeeder::class,
            // ContactRequestSeeder::class,
            // Datos reales del menú Little Claire
            RealDataSeeder::class,
        ]);
    }
}
