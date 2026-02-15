<?php

use App\Models\Menu;
use App\Models\MenuProduct;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list products of a menu', function () {
    $menu = Menu::factory()->create();
    $products = Product::factory()->count(2)->create();

    foreach ($products as $index => $product) {
        MenuProduct::create([
            'menu_id' => $menu->id,
            'product_id' => $product->id,
            'position' => $index + 1,
            'custom_price' => null,
        ]);
    }

    $response = $this->getJson("/api/menus/{$menu->id}/products");

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

test('can attach product to menu', function () {
    $menu = Menu::factory()->create();
    $product = Product::factory()->create();

    $response = $this->postJson("/api/menus/{$menu->id}/products", [
        'product_id' => $product->id,
        'position' => 1,
        'custom_price' => 9.99,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('menu_products', [
        'menu_id' => $menu->id,
        'product_id' => $product->id,
    ]);
});


test('can reorder products inside menu', function () {
    $menu = Menu::factory()->create();
    $products = Product::factory()->count(2)->create();

    foreach ($products as $index => $product) {
        MenuProduct::create([
            'menu_id' => $menu->id,
            'product_id' => $product->id,
            'position' => $index + 1,
            'custom_price' => null,
        ]);
    }

    $response = $this->putJson("/api/menus/{$menu->id}/products/order", [
        'products' => [
            ['id' => $products[0]->id, 'position' => 2],
            ['id' => $products[1]->id, 'position' => 1],
        ]
    ]);

    $response->assertStatus(204);

    $this->assertDatabaseHas('menu_products', [
        'menu_id' => $menu->id,
        'product_id' => $products[0]->id,
        'position' => 2,
    ]);

    $this->assertDatabaseHas('menu_products', [
        'menu_id' => $menu->id,
        'product_id' => $products[1]->id,
        'position' => 1,
    ]);
});

test('can update custom price of product in menu', function () {
    $menu = Menu::factory()->create();
    $product = Product::factory()->create();

    MenuProduct::create([
        'menu_id' => $menu->id,
        'product_id' => $product->id,
        'position' => 1,
        'custom_price' => null,
    ]);

    $response = $this->putJson("/api/menus/{$menu->id}/products/{$product->id}", [
        'custom_price' => 12.50,
    ]);

    $response->assertStatus(204);

    $this->assertDatabaseHas('menu_products', [
        'menu_id' => $menu->id,
        'product_id' => $product->id,
        'custom_price' => 12.50,
    ]);
});
