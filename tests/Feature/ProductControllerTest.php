<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

test('can list products', function () {
    Product::factory()->count(2)->create();

    /** @var \Tests\TestCase $this */
    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('can create product', function () {
    $category = Category::factory()->create();

    /** @var \Tests\TestCase $this */
    $response = $this->postJson('/api/products', [
        'category_id' => $category->id,
        'name' => 'Latte',
        'description' => null,
        'price' => 5.5,
        'is_active' => true,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('products', [
        'name' => 'Latte',
        'category_id' => $category->id,
    ]);
});

test('can update product', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Old name',
    ]);

    $response = $this->putJson("/api/products/{$product->id}", [
        'category_id' => $category->id,
        'name' => 'New name',
        'description' => null,
        'price' => 7.0,
        'is_active' => true,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'New name',
    ]);
});

test('can delete product', function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
