<?php

use App\Models\Box;
use App\Models\Item;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('items can be retrieved', function () {
    $box = Box::factory()->hasItems(3)->create();

    $response = $this->getJson("/api/boxes/{$box->id}/items");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => $box->items->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'photo_path' => $item->photo_path,
                'box_id' => $item->box_id,
                'created_at' => $item->created_at->toJSON(),
                'updated_at' => $item->updated_at->toJSON(),
            ])->toArray()
        ]);
});

test('a single item can be retrieved', function () {
    $box = Box::factory()->create();
    $item = Item::factory()->create(['box_id' => $box->id]);

    $response = $this->getJson("/api/boxes/{$box->id}/items/{$item->id}");

    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'photo_path' => $item->photo_path,
                'box_id' => $item->box_id,
                'created_at' => $item->created_at->toJSON(),
                'updated_at' => $item->updated_at->toJSON(),
            ]
        ]);
});

test('getting a non-existent item returns a 404', function () {
    $box = Box::factory()->create();
    $response = $this->getJson("/api/boxes/{$box->id}/items/999");

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('an item can be stored', function () {
    $box = Box::factory()->create();
    $itemData = Item::factory()->raw(['box_id' => $box->id]);

    Storage::fake('public');
    // Create a fake image file for testing
    $file = UploadedFile::fake()->image('test-image.jpg', 600, 400);
    $itemData['photo'] = $file;

    $response = $this->postJson("/api/boxes/{$box->id}/items", $itemData);

    $response->assertStatus(Response::HTTP_CREATED)
        ->assertJson([
            'data' => [
                'name' => $itemData['name'],
                'description' => $itemData['description'],
                'box_id' => $box->id
            ]
        ]);

    // Check that the file was stored
    $this->assertTrue(Storage::disk('public')->exists('photos/' . $file->hashName()));

    $item = Item::first();

    expect($item)
        ->name->toBe($itemData['name'])
        ->description->toBe($itemData['description'])
        ->photo_path->toBe('photos/' . $file->hashName()) // Check the stored path
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
    $box = Box::factory()->create();
    $response = $this->postJson("/api/boxes/{$box->id}/items", []);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name']);
});

test('box_id is required when creating an item', function () {
    $response = $this->postJson('/api/boxes/999/items', [
        'name' => 'Coffee Mug'
    ]);

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('box must exist when creating an item', function () {
    $response = $this->postJson('/api/boxes/999/items', [
        'name' => 'Coffee Mug'
    ]);

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('description and photo_path are optional when creating an item', function () {
    $box = Box::factory()->create();
    $itemData = [
        'name' => 'Coffee Mug'
    ];

    $response = $this->postJson("/api/boxes/{$box->id}/items", $itemData);

    $response->assertStatus(Response::HTTP_CREATED);

    expect(Item::first())
        ->description->toBeNull()
        ->photo_path->toBeNull();
});

test('an item can be deleted', function () {
    $box = Box::factory()->hasItems(1)->create();

    $response = $this->deleteJson("/api/boxes/{$box->id}/items/{$box->items->first()->id}");

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    expect(Item::count())->toBe(0);
});

test('deleting a non-existent item returns a 404', function () {
    $box = Box::factory()->create();
    $response = $this->deleteJson("/api/boxes/{$box->id}/items/999");

    $response->assertStatus(Response::HTTP_NOT_FOUND);
});

test('deleting a box also deletes its items', function () {
    $box = Box::factory()->hasItems(3)->create();

    $box->delete();

    expect(Item::count())->toBe(0);
});

test('an item can be updated', function () {
    $box = Box::factory()->create();
    $item = Item::factory()->create(['box_id' => $box->id]);

    $updatedItem = [
        'name' => 'Updated Coffee Mug',
        'description' => 'Updated description',
        'photo_path' => 'items/updated-coffee-mug.jpg'
    ];
    $response = $this->putJson("/api/boxes/{$box->id}/items/{$item->id}", $updatedItem);

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
    $box = Box::factory()->hasItems(1)->create();

    $response = $this->putJson("/api/boxes/{$box->id}/items/{$box->items->first()->id}", [
        'description' => null,
        'photo_path' => null
    ]);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['name']);
});

test('description can be set to null', function () {
    $box = Box::factory()->create();
    $item = Item::factory()->create([
        'box_id' => $box->id,
        'description' => 'Original description'
    ]);

    $response = $this->putJson("/api/boxes/{$box->id}/items/{$item->id}", [
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
    $box = Box::factory()->create();
    $item = Item::factory()->create([
        'box_id' => $box->id,
        'photo_path' => 'items/original.jpg'
    ]);

    $response = $this->putJson("/api/boxes/{$box->id}/items/{$item->id}", [
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

    $response = $this->putJson("/api/boxes/{$originalBox->id}/items/{$item->id}", [
        'name' => 'Updated Coffee Mug',
        'description' => null,
        'photo_path' => null
    ]);

    $response->assertStatus(Response::HTTP_OK);

    expect($item->fresh())
        ->box_id->toBe($originalBox->id);
});
