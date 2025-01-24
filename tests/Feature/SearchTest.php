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

    // Search for "kitchen" - should find the kitchen box but no items
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=kitchen');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                [
                    'id' => $kitchenBox->id,
                    'name' => $kitchenBox->name,
                    'description' => $kitchenBox->description,
                    'location' => $kitchenBox->location,
                    'items' => []
                ]
            ]
        ]);

    // Search for "stapler" - should find the office box with the stapler item
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=stapler');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                [
                    'id' => $officeBox->id,
                    'name' => $officeBox->name,
                    'description' => $officeBox->description,
                    'location' => $officeBox->location,
                    'items' => [
                        [
                            'id' => $officeItem->id,
                            'name' => $officeItem->name,
                            'description' => $officeItem->description,
                            'box_id' => $officeBox->id,
                        ]
                    ]
                ]
            ]
        ]);

    // Search for "mug" - should find the kitchen box with the coffee mug item
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->getJson('/api/search?q=mug');
    $response->assertStatus(Response::HTTP_OK)
        ->assertJson([
            'data' => [
                [
                    'id' => $kitchenBox->id,
                    'name' => $kitchenBox->name,
                    'description' => $kitchenBox->description,
                    'location' => $kitchenBox->location,
                    'items' => [
                        [
                            'id' => $kitchenItem->id,
                            'name' => $kitchenItem->name,
                            'description' => $kitchenItem->description,
                            'box_id' => $kitchenBox->id,
                        ]
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
            'data' => []
        ]);
}); 