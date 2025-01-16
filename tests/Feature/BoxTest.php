<?php

use App\Models\Box;

test('a box can be stored', function () {
    $boxData = [
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ];

    $response = $this->postJson('/api/boxes', $boxData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => 'Kitchen Supplies',
                'description' => 'Contains kitchen utensils and supplies',
                'location' => 'Kitchen Cabinet'
            ]
        ]);

    $box = Box::first();

    expect($box)
        ->name->toBe('Kitchen Supplies')
        ->description->toBe('Contains kitchen utensils and supplies')
        ->location->toBe('Kitchen Cabinet')
        ->id->toBeInt()
        ->created_at->not->toBeNull()
        ->updated_at->not->toBeNull();
});

test('name and location are required when creating a box', function () {
    $response = $this->postJson('/api/boxes', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'location']);
});

test('description is optional when creating a box', function () {
    $boxData = [
        'name' => 'Kitchen Supplies',
        'location' => 'Kitchen Cabinet'
    ];

    $response = $this->postJson('/api/boxes', $boxData);

    $response->assertStatus(201);

    expect(Box::first())
        ->description->toBeNull();
});
