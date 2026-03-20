<?php

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(Admin::factory()->create());
});

test('can list images of a variant', function () {
    $variant = ProductVariant::factory()->create();
    ProductImage::factory()->create(['product_variant_id' => $variant->id, 'position' => 1]);
    ProductImage::factory()->create(['product_variant_id' => $variant->id, 'position' => 2]);

    $response = $this->getJson("/api/products/{$variant->product_id}/variants/{$variant->id}/images");

    $response->assertStatus(200)->assertJsonCount(2);
});

test('can add an image to a variant', function () {
    $variant = ProductVariant::factory()->create();

    $response = $this->postJson("/api/products/{$variant->product_id}/variants/{$variant->id}/images", [
        'image_url' => 'https://placehold.co/600x400/png',
        'position' => 1,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('product_images', [
        'product_variant_id' => $variant->id,
        'position' => 1,
    ]);
});

test('store fails without image_url', function () {
    $variant = ProductVariant::factory()->create();

    $response = $this->postJson("/api/products/{$variant->product_id}/variants/{$variant->id}/images", [
        'position' => 1,
    ]);

    $response->assertStatus(422)->assertJsonValidationErrors('image_url');
});

test('can update an image', function () {
    $variant = ProductVariant::factory()->create();
    $image = ProductImage::factory()->create([
        'product_variant_id' => $variant->id,
        'position' => 1,
    ]);

    $response = $this->putJson("/api/products/{$variant->product_id}/variants/{$variant->id}/images/{$image->id}", [
        'image_url' => 'https://placehold.co/800x600/png',
        'position' => 2,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('product_images', [
        'id' => $image->id,
        'image_url' => 'https://placehold.co/800x600/png',
        'position' => 2,
    ]);
});

test('can delete an image', function () {
    $variant = ProductVariant::factory()->create();
    $image = ProductImage::factory()->create([
        'product_variant_id' => $variant->id,
        'position' => 1,
    ]);

    $response = $this->deleteJson("/api/products/{$variant->product_id}/variants/{$variant->id}/images/{$image->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('product_images', ['id' => $image->id]);
});
