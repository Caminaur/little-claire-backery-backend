<?php

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can login with valid credentials', function () {
    $admin = Admin::factory()->create([
        'email' => 'admin@test.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@test.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(204);
});

test('login fails with wrong password', function () {
    Admin::factory()->create([
        'email' => 'admin@test.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->postJson('/api/admin/login', [
        'email' => 'admin@test.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422);
});

test('login fails with unknown email', function () {
    $response = $this->postJson('/api/admin/login', [
        'email' => 'noexiste@test.com',
        'password' => 'secret123',
    ]);

    $response->assertStatus(422);
});

test('me returns authenticated admin data', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin)->getJson('/api/admin/me');

    $response->assertStatus(200)
        ->assertJsonFragment(['email' => $admin->email]);
});

test('me returns 401 when unauthenticated', function () {
    $response = $this->getJson('/api/admin/me');

    $response->assertStatus(401);
});

test('admin can logout', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'web')->postJson('/api/admin/logout');

    $response->assertStatus(204);
});
