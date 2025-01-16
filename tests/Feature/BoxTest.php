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

    $response = $this->getJson('/api/boxes');

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

test('a box can be updated', function () {
    $box = Box::create([
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ]);

    $response = $this->putJson("/api/boxes/{$box->id}", [
        'name' => 'Updated Kitchen Supplies',
        'description' => 'Updated description',
        'location' => 'Updated location'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'name' => 'Updated Kitchen Supplies',
                'description' => 'Updated description',
                'location' => 'Updated location'
            ]
        ]);

    expect($box->fresh())
        ->name->toBe('Updated Kitchen Supplies')
        ->description->toBe('Updated description')
        ->location->toBe('Updated location');
});

test('only name, description and location can be updated on a box', function () {
    $box = Box::create([
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ]);

    $originalCreatedAt = $box->created_at;
    $originalId = $box->id;

    $response = $this->putJson("/api/boxes/{$box->id}", [
        'id' => 999,
        'name' => 'Updated Kitchen Supplies',
        'description' => 'Updated description',
        'location' => 'Updated location',
        'created_at' => now()->addDay()
    ]);

    $response->assertStatus(200);

    $box->refresh();
    
    expect($box)
        ->name->toBe('Updated Kitchen Supplies')
        ->description->toBe('Updated description')
        ->location->toBe('Updated location')
        ->id->toBe($originalId)
        ->created_at->toEqual($originalCreatedAt);
});

test('name and location are required when updating a box', function () {
    $box = Box::create([
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ]);

    $response = $this->putJson("/api/boxes/{$box->id}", [
        'description' => 'Updated description'
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'location']);
});

test('a box can be deleted', function () {
    $box = Box::create([
        'name' => 'Kitchen Supplies',
        'description' => 'Contains kitchen utensils and supplies',
        'location' => 'Kitchen Cabinet'
    ]);

    $response = $this->deleteJson("/api/boxes/{$box->id}");

    $response->assertStatus(204);

    expect(Box::count())->toBe(0);
});

test('deleting a non-existent box returns a 404', function () {
    $response = $this->deleteJson("/api/boxes/1");

    $response->assertStatus(404);
});
