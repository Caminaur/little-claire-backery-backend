<?php

use App\Models\ContactRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Make a couple Requests', function () {
    ContactRequest::factory(22)->create();

    $response = $this->getJson('/api/contact-requests');

    $response->assertStatus(200)->assertJsonCount(22);
});

test('Fail on invalid ENUM value', function () {

    $response = $this->postJson('/api/contact-requests', [
        "name" => "recusandae",
        "email" => "gleason.estefania@yahoo.com",
        "phone" => "(949) 928-4663",
        "message" => "Ipsam delectus mollitia sed exercitationem. Omnis corrupti sed esse commodi id et eum.",
        "type" => "Spontaneus",
    ]);


    $response->assertStatus(422)->assertJsonValidationErrors('type');
});

test('Fail when name and email are missing', function () {

    $response = $this->postJson('/api/contact-requests', [
        "phone" => "(949) 928-4663",
        "message" => "",
        "type" => "catering",
    ]);


    $response->assertStatus(422)->assertJsonValidationErrors(['name', 'email']);
});
