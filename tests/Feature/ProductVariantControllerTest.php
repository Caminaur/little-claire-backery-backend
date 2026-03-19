<?php

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(Admin::factory()->create());
});

test('can list variants of a product', function () {
    $product = Product::factory()->create();
    ProductVariant::factory()->count(3)->create(['product_id' => $product->id]);

    $response = $this->getJson("/api/products/{$product->id}/variants");

    $response->assertStatus(200)->assertJsonCount(3);
});

test('can create a variant', function () {
    $product = Product::factory()->create();

    $response = $this->postJson("/api/products/{$product->id}/variants", [
        'label' => 'Grande',
        'price' => 4.50,
        'position' => 1,
        'is_active' => true,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('product_variants', [
        'product_id' => $product->id,
        'label' => 'Grande',
        'price' => 4.50,
    ]);
});

test('can create a variant without label (single-price product)', function () {
    $product = Product::factory()->create();

    $response = $this->postJson("/api/products/{$product->id}/variants", [
        'label' => null,
        'price' => 3.00,
        'position' => 1,
        'is_active' => true,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('product_variants', [
        'product_id' => $product->id,
        'label' => null,
        'price' => 3.00,
    ]);
});

test('create variant fails without price', function () {
    $product = Product::factory()->create();

    $response = $this->postJson("/api/products/{$product->id}/variants", [
        'label' => 'Chico',
        'position' => 1,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('price');
});

test('can update a variant', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'label' => 'Chico',
        'price' => 2.50,
    ]);

    $response = $this->putJson("/api/products/{$product->id}/variants/{$variant->id}", [
        'label' => 'Grande',
        'price' => 5.00,
        'position' => 1,
        'is_active' => true,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('product_variants', [
        'id' => $variant->id,
        'label' => 'Grande',
        'price' => 5.00,
    ]);
});

test('can delete a variant', function () {
    $product = Product::factory()->create();
    $variant = ProductVariant::factory()->create(['product_id' => $product->id]);

    $response = $this->deleteJson("/api/products/{$product->id}/variants/{$variant->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('product_variants', ['id' => $variant->id]);
});
