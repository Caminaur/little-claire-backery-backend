<?php

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list categories', function () {
    Category::factory()->count(2)->create();

    $response = $this->getJson('/api/categories');

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('can create category', function () {
    $response = $this->postJson('/api/categories', [
        'name' => 'Coffee',
        'is_visible' => true,
        'position' => 1,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('categories', [
        'name' => 'Coffee',
    ]);
});

uses(RefreshDatabase::class);

test('can update category', function () {
    $category = Category::factory()->create([
        'name' => 'Old name',
        'is_visible' => false,
    ]);

    $response = $this->putJson("/api/categories/{$category->id}", [
        'name' => 'New name',
        'description' => null,
        'image_url' => null,
        'is_visible' => true,
        'position' => 2,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'New name',
        'is_visible' => true,
    ]);
});

test('can delete category', function () {
    $category = Category::factory()->create();

    $response = $this->deleteJson("/api/categories/{$category->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
    ]);
});
