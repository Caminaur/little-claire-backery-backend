<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('price_display', ['auto', 'price_box', 'inline_banner'])->default('auto')->after('is_visible');
            $table->boolean('is_full_width')->default(false)->after('price_display');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['price_display', 'is_full_width']);
        });
    }
};
