<?php

use App\Models\Box;
use App\Models\Item;
use Illuminate\Http\Response;

test('an item can be stored', function () {
    $box = Box::factory()->create();
    $itemData = Item::factory()->raw(['box_id' => $box->id]);

    $response = $this->postJson('/api/items', $itemData);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson([
            'data' => [
                'name' => $itemData['name'],
                'description' => $itemData['description'],
                'photo_path' => $itemData['photo_path'],
                'box_id' => $box->id
            ]
        ]);

    $item = Item::first();

    expect($item)
        ->name->toBe($itemData['name'])
        ->description->toBe($itemData['description'])
        ->photo_path->toBe($itemData['photo_path'])
        ->box_id->toBe($box->id)
        ->id->toBeInt()
        ->created_at->not->toBeNull()
        ->updated_at->not->toBeNull();
});

test('an item belongs to a box', function () {
    $box = Box::factory()->create();
    $item = Item::factory()->create(['box_id' => $box->id]);

    expect($item->box)->toBeInstanceOf(Box::class)
        ->and($item->box->id)->toBe($box->id);
});

test('name is required when creating an item', function () {
    $response = $this->postJson('/api/items', [
        'box_id' => Box::factory()->create()->id
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name']);
});

test('box_id is required when creating an item', function () {
    $response = $this->postJson('/api/items', [
        'name' => 'Coffee Mug'
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['box_id']);
});

test('box must exist when creating an item', function () {
    $response = $this->postJson('/api/items', [
        'name' => 'Coffee Mug',
        'box_id' => 999
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['box_id']);
});

test('description and photo_path are optional when creating an item', function () {
    $box = Box::factory()->create();
    $itemData = [
        'name' => 'Coffee Mug',
        'box_id' => $box->id
    ];

    $response = $this->postJson('/api/items', $itemData);

    $response->assertStatus(Response::HTTP_CREATED);

    expect(Item::first())
        ->description->toBeNull()
        ->photo_path->toBeNull();
});

test('an item can be deleted', function () {
    $item = Item::factory()->create();

    $response = $this->deleteJson("/api/items/{$item->id}");

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    expect(Item::count())->toBe(0);
});

test('deleting a non-existent item returns a 404', function () {
    $response = $this->deleteJson("/api/items/1");

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('deleting a box also deletes its items', function () {
    $box = Box::factory()->create();
    Item::factory()->count(3)->create(['box_id' => $box->id]);

    $box->delete();

    expect(Item::count())->toBe(0);
});

test('an item can be updated', function () {
    $item = Item::factory()->create();

    $updatedItem = [
        'name' => 'Updated Coffee Mug',
        'description' => 'Updated description',
        'photo_path' => 'items/updated-coffee-mug.jpg'
    ];
    $response = $this->putJson("/api/items/{$item->id}", $updatedItem);

    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => $updatedItem
        ]);

    expect($item->fresh())
        ->name->toBe($updatedItem['name'])
        ->description->toBe($updatedItem['description'])
        ->photo_path->toBe($updatedItem['photo_path']);
});

test('name is required when updating an item', function () {
    $item = Item::factory()->create();

    $response = $this->putJson("/api/items/{$item->id}", [
        'description' => null,
        'photo_path' => null
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name']);
});

test('description can be set to null', function () {
    $item = Item::factory()->create(['description' => 'Original description']);

    $response = $this->putJson("/api/items/{$item->id}", [
        'name' => 'Updated Coffee Mug',
        'description' => null,
        'photo_path' => null
    ]);

    $response->assertStatus(Response::HTTP_OK);

    expect($item->fresh())
        ->description->toBeNull()
        ->photo_path->toBeNull();
});

test('photo_path can be set to null', function () {
    $item = Item::factory()->create(['photo_path' => 'items/original.jpg']);

    $response = $this->putJson("/api/items/{$item->id}", [
        'name' => 'Updated Coffee Mug',
        'description' => null,
        'photo_path' => null
    ]);

    $response->assertStatus(Response::HTTP_OK);

    expect($item->fresh())
        ->photo_path->toBeNull();
});

test('box_id cannot be updated', function () {
    $originalBox = Box::factory()->create();
    $newBox = Box::factory()->create();

    $item = Item::factory()->create(['box_id' => $originalBox->id]);

    $response = $this->putJson("/api/items/{$item->id}", [
        'name' => 'Updated Coffee Mug',
        'description' => null,
        'photo_path' => null,
        'box_id' => $newBox->id
    ]);

    $response->assertStatus(Response::HTTP_OK);

    expect($item->fresh())
        ->box_id->toBe($originalBox->id);
});
