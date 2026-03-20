<?php

use App\Models\Admin;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(Admin::factory()->create());
});

test('can list products of a promotion', function () {
    $promotion = Promotion::factory()->create();
    $products = Product::factory()->count(2)->create();
    $promotion->products()->attach($products->pluck('id'));

    $response = $this->getJson("/api/promotions/{$promotion->id}/products");

    $response->assertStatus(200)->assertJsonCount(2);
});

test('can attach product to promotion', function () {
    $promotion = Promotion::factory()->create();
    $product = Product::factory()->create();

    $response = $this->postJson("/api/promotions/{$promotion->id}/products", [
        'product_id' => $product->id,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('promotion_products', [
        'promotion_id' => $promotion->id,
        'product_id' => $product->id,
    ]);
});

test('cannot attach same product twice', function () {
    $promotion = Promotion::factory()->create();
    $product = Product::factory()->create();
    $promotion->products()->attach($product->id);

    $response = $this->postJson("/api/promotions/{$promotion->id}/products", [
        'product_id' => $product->id,
    ]);

    $response->assertStatus(409);
});

test('can detach product from promotion', function () {
    $promotion = Promotion::factory()->create();
    $product = Product::factory()->create();
    $promotion->products()->attach($product->id);

    $response = $this->deleteJson("/api/promotions/{$promotion->id}/products/{$product->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('promotion_products', [
        'promotion_id' => $promotion->id,
        'product_id' => $product->id,
    ]);
});
