<?php

use App\Models\Box;
use App\Models\Item;
use Illuminate\Http\Response;

test('all boxes can be retrieved', function () {
    $boxes = Box::factory()->count(3)->create();

    $response = $this->getJson('/api/boxes');

    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => $boxes->map(fn ($box) => [
                'id' => $box->id,
                'name' => $box->name,
                'description' => $box->description,
                'location' => $box->location,
                'created_at' => $box->created_at->toJSON(),
                'updated_at' => $box->updated_at->toJSON(),
            ])->toArray()
        ]);
});

test('a box can be retrieved', function () {
    $box = Box::factory()->create();

    $response = $this->getJson("/api/boxes/{$box->id}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                'id' => $box->id,
                'name' => $box->name,
                'description' => $box->description,
                'location' => $box->location,
                'created_at' => $box->created_at->toJSON(),
                'updated_at' => $box->updated_at->toJSON(),
            ]
        ]);
});

test('getting a non-existent box returns a 404', function () {
    $response = $this->getJson('/api/boxes/999');
    
    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('a box can be stored', function () {
    $boxData = Box::factory()->raw();

    $response = $this->postJson('/api/boxes', $boxData);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson([
            'data' => [
                'name' => $boxData['name'],
                'description' => $boxData['description'],
                'location' => $boxData['location']
            ]
        ]);

    $box = Box::first();

    expect($box)
        ->name->toBe($boxData['name'])
        ->description->toBe($boxData['description'])
        ->location->toBe($boxData['location'])
        ->id->toBeInt()
        ->created_at->not->toBeNull()
        ->updated_at->not->toBeNull();
});

test('name and location are required when creating a box', function () {
    $response = $this->postJson('/api/boxes', []);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name', 'location']);
});

test('description is optional when creating a box', function () {
    $boxData = [
        'name' => 'Kitchen Supplies',
        'location' => 'Kitchen Cabinet'
    ];

    $response = $this->postJson('/api/boxes', $boxData);

    $response->assertStatus(Response::HTTP_CREATED);

    expect(Box::first())
        ->description->toBeNull();
});

test('a box can be updated', function () {
    $box = Box::factory()->create();

    $updatedBox = [
        'name' => 'Updated Kitchen Supplies',
        'description' => 'Updated description',
        'location' => 'Updated location'
    ];

    $response = $this->putJson("/api/boxes/{$box->id}", $updatedBox);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => $updatedBox
        ]);

    expect($box->fresh())
        ->name->toBe($updatedBox['name'])
        ->description->toBe($updatedBox['description'])
        ->location->toBe($updatedBox['location']);
});

test('only name, description and location can be updated on a box', function () {
    $box = Box::factory()->create();

    $originalCreatedAt = $box->created_at;
    $originalId = $box->id;

    $response = $this->putJson("/api/boxes/{$box->id}", [
        'id' => 999,
        'name' => 'Updated Kitchen Supplies',
        'description' => 'Updated description',
        'location' => 'Updated location',
        'created_at' => now()->addDay()
    ]);

    $response->assertStatus(Response::HTTP_OK);

    $box->refresh();

    expect($box)
        ->name->toBe('Updated Kitchen Supplies')
        ->description->toBe('Updated description')
        ->location->toBe('Updated location')
        ->id->toBe($originalId)
        ->created_at->toEqual($originalCreatedAt);
});

test('name and location are required when updating a box', function () {
    $box = Box::factory()->create();

    $response = $this->putJson("/api/boxes/{$box->id}", [
        'description' => 'Updated description'
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name', 'location']);
});

test('a box can be deleted', function () {
    $box = Box::factory()->create();

    $response = $this->deleteJson("/api/boxes/{$box->id}");

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    expect(Box::count())->toBe(0);
});

test('deleting a non-existent box returns a 404', function () {
    $response = $this->deleteJson("/api/boxes/1");

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('a box can have many items', function () {
    $box = Box::factory()->hasItems(3)->create();

    expect($box->items)->toHaveCount(3)
        ->and($box->items->first())->toBeInstanceOf(Item::class);
});
