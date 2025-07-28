<?php

use App\Models\User;

test('the property index renders', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/properties');

    $response->assertStatus(200);
});

test('the property creation page renders', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/property');

    $response->assertStatus(200);
});
