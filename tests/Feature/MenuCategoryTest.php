<?php

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list categories of a menu', function () {
    $menu = Menu::factory()->create();
    $categories = Category::factory()->count(3)->create();

    foreach ($categories as $index => $category) {
        MenuCategory::create([
            'menu_id' => $menu->id,
            'category_id' => $category->id,
            'position' => $index + 1,
        ]);
    }

    $response = $this->getJson("/api/menus/{$menu->id}/categories");

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});


test('can attach category to menu', function () {
    $menu = Menu::factory()->create();
    $category = Category::factory()->create();

    $response = $this->postJson("/api/menus/{$menu->id}/categories", [
        'category_id' => $category->id,
        'position' => 1,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('menu_categories', [
        'menu_id' => $menu->id,
        'category_id' => $category->id,
    ]);
});

test('cannot attach same category twice', function () {
    $menu = Menu::factory()->create();
    $category = Category::factory()->create();

    MenuCategory::create([
        'menu_id' => $menu->id,
        'category_id' => $category->id,
        'position' => 1,
    ]);

    $response = $this->postJson("/api/menus/{$menu->id}/categories", [
        'category_id' => $category->id,
        'position' => 2,
    ]);

    $response->assertStatus(409);
});


test('can reorder categories in menu', function () {
    $menu = Menu::factory()->create();
    $categories = Category::factory()->count(2)->create();

    foreach ($categories as $index => $category) {
        MenuCategory::create([
            'menu_id' => $menu->id,
            'category_id' => $category->id,
            'position' => $index + 1,
        ]);
    }

    $response = $this->putJson("/api/menus/{$menu->id}/categories/order", [
        'categories' => [
            ['id' => $categories[0]->id, 'position' => 2],
            ['id' => $categories[1]->id, 'position' => 1],
        ]
    ]);

    $response->assertStatus(204);

    $this->assertDatabaseHas('menu_categories', [
        'menu_id' => $menu->id,
        'category_id' => $categories[0]->id,
        'position' => 2,
    ]);
});


test('can detach category from menu', function () {
    $menu = Menu::factory()->create();
    $category = Category::factory()->create();

    MenuCategory::create([
        'menu_id' => $menu->id,
        'category_id' => $category->id,
        'position' => 1,
    ]);

    $response = $this->deleteJson("/api/menus/{$menu->id}/categories/{$category->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('menu_categories', [
        'menu_id' => $menu->id,
        'category_id' => $category->id,
    ]);
});
