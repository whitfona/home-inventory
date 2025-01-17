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
