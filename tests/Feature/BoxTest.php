<?php

use App\Models\Box;

test('all boxes can be retrieved', function() {
    Box::create([
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ]);

    Box::create([
        'name' => 'Tools',
        'description' => 'Contains various tools',
        'location' => 'Garage'
    ]);

    $response = $this->getJson('/boxes');

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                [
                    'name' => 'Kitchen Supplies',
                    'description' => 'Contains kitchen utensils and supplies',
                    'location' => 'Kitchen Cabinet'
                ],
                [
                    'name' => 'Tools',
                    'description' => 'Contains various tools',
                    'location' => 'Garage'
                ]
            ]
        ]);
});

test('a box can be stored', function () {
    $boxData = [
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ];

    $response = $this->postJson('/boxes', $boxData);

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
    $response = $this->postJson('/boxes', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'location']);
});

test('description is optional when creating a box', function () {
    $boxData = [
        'name' => 'Kitchen Supplies',
        'location' => 'Kitchen Cabinet'
    ];

    $response = $this->postJson('/boxes', $boxData);

    $response->assertStatus(201);

    expect(Box::first())
        ->description->toBeNull();
});
