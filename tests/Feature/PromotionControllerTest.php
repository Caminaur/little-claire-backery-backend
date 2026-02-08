<?php

use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can list promotions', function () {
    Promotion::factory()->count(5)->create();


    $response = $this->getJson("/api/promotions");

    $response->assertStatus(200)->assertJsonCount(5);
});

test('can create a Promotion', function () {
    $promotion = Promotion::factory()->create();

    $response = $this->postJson('/api/promotions', [
        'id' => $promotion->id,
        'title' => $promotion->title,
        'description' => $promotion->description,
        'discount_type' => $promotion->discount_type,
        'discount_value' => $promotion->discount_value,
        'starts_at' => $promotion->starts_at,
        'ends_at' => $promotion->ends_at,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas(
        'promotions',
        [
            'id' => $promotion->id,
            'title' => $promotion->title,
        ]
    );
});

test('can update a Promotion', function () {

    $promotion = Promotion::factory()->create();

    $response = $this->putJson("/api/promotions/{$promotion->id}", [
        'id' => $promotion->id,
        'title' => 'Super Promotion',
        'discount_type' => $promotion->discount_type,
        'discount_value' => $promotion->discount_value,
        'is_active' => $promotion->is_active
    ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas(
        'promotions',
        [
            'id' => $promotion->id,
            'title' => "Super Promotion"
        ]
    );
});

test("can delete a Promotion", function () {

    $promotion = Promotion::factory()->create();

    $response = $this->deleteJson("/api/promotions/{$promotion->id}");

    $response->assertStatus(204);

    $this->assertDatabaseMissing(
        'promotions',
        [
            'id' => $promotion->id,
        ]
    );
});
