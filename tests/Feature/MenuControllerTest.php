<?php

use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list Menus', function () {
    Menu::factory()->count(2)->create();

    $response = $this->getJson('/api/menus');

    $response->assertStatus(200)
        ->assertJsonCount(2);
});

test('can create Menu', function () {
    $response = $this->postJson('/api/menus', [
        "name" => "█▄█▄██▄█▄█▄█",
        "description" => "Ipsum aut nam quasi et debitis blanditiis illo et provident.",
        "is_active" => false
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('menus', [
        'name' => '█▄█▄██▄█▄█▄█',
    ]);
});

test('can update a Menu', function () {
    $menu = Menu::factory()->create([
        'name' => 'Old name',
        'is_active' => false,
    ]);

    $response = $this->putJson("/api/menus/{$menu->id}", [
        'name' => 'New name',
        'description' => null,
        'is_active' => true,
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('menus', [
        'id' => $menu->id,
        'name' => 'New name',
        'is_active' => true,
    ]);
});

test('can delete Menu', function () {
    $menu = Menu::factory()->create();

    $response = $this->deleteJson("/api/menus/{$menu->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing('menus', [
        'id' => $menu->id,
    ]);
});
