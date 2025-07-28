<?php

use App\Models\User;

test('the customer index renders', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/customers');

    $response->assertStatus(200);
});

test('the customer creation page renders', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/customer');

    $response->assertStatus(200);
});
