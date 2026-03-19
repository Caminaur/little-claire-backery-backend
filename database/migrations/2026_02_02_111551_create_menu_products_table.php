<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu_products', function (Blueprint $table) {
            $table->foreignId('menu_id')
                ->constrained('menus', 'id')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products', 'id')
                ->cascadeOnDelete();

            $table->unsignedSmallInteger('position');

            // custom_price removed: prices are owned by product_variants
            $table->unique(['menu_id', 'product_id']); // evitamos duplicar producto en el menu
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_products');
    }
};
