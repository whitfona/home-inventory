<?php

use App\Models\Box;
use App\Models\Item;
use Illuminate\Http\Response;

test('it can search boxes and items', function () {
    // Create boxes with searchable content
    $kitchenBox = Box::factory()->create([
        'name' => 'Kitchen Supplies',
        'description' => 'Contains cooking utensils',
        'location' => 'Kitchen Cabinet'
    ]);

    $officeBox = Box::factory()->create([
        'name' => 'Office Items',
        'description' => 'Contains office supplies',
        'location' => 'Office Desk'
    ]);

    // Create items with searchable content
    $kitchenItem = Item::factory()->create([
        'name' => 'Coffee Mug',
        'description' => 'A ceramic mug for hot beverages',
        'box_id' => $kitchenBox->id
    ]);

    $officeItem = Item::factory()->create([
        'name' => 'Stapler',
        'description' => 'Red Swingline stapler',
        'box_id' => $officeBox->id
    ]);

    // Search for "kitchen" - should find the kitchen box and its items
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=kitchen');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                'boxes' => [
                    [
                        'id' => $kitchenBox->id,
                        'name' => $kitchenBox->name,
                        'description' => $kitchenBox->description,
                        'location' => $kitchenBox->location,
                        'created_at' => $kitchenBox->created_at->toJSON(),
                        'updated_at' => $kitchenBox->updated_at->toJSON()
                    ]
                ],
                'items' => [
                    [
                        'id' => $kitchenItem->id,
                        'name' => $kitchenItem->name,
                        'description' => $kitchenItem->description,
                        'box_id' => $kitchenBox->id,
                        'photo_path' => $kitchenItem->photo_path,
                        'created_at' => $kitchenItem->created_at->toJSON(),
                        'updated_at' => $kitchenItem->updated_at->toJSON()
                    ]
                ]
            ]
        ]);

    // Search for "stapler" - should find only the stapler item
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=stapler');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                'boxes' => [],
                'items' => [
                    [
                        'id' => $officeItem->id,
                        'name' => $officeItem->name,
                        'description' => $officeItem->description,
                        'box_id' => $officeBox->id,
                        'photo_path' => $officeItem->photo_path,
                        'created_at' => $officeItem->created_at->toJSON(),
                        'updated_at' => $officeItem->updated_at->toJSON()
                    ]
                ]
            ]
        ]);

    // Search for "office" - should find the office box and its items
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=office');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                'boxes' => [
                    [
                        'id' => $officeBox->id,
                        'name' => $officeBox->name,
                        'description' => $officeBox->description,
                        'location' => $officeBox->location,
                        'created_at' => $officeBox->created_at->toJSON(),
                        'updated_at' => $officeBox->updated_at->toJSON()
                    ]
                ],
                'items' => [
                    [
                        'id' => $officeItem->id,
                        'name' => $officeItem->name,
                        'description' => $officeItem->description,
                        'box_id' => $officeBox->id,
                        'photo_path' => $officeItem->photo_path,
                        'created_at' => $officeItem->created_at->toJSON(),
                        'updated_at' => $officeItem->updated_at->toJSON()
                    ]
                ]
            ]
        ]);
});

test('search query is required', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search');
    
    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['q']);
});

test('empty search returns empty results', function () {
    Box::factory()->create();
    Item::factory()->create();

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=nonexistentterm');
    
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                'boxes' => [],
                'items' => []
            ]
        ]);
}); 