<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoxFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'location' => fake()->words(2, true),
            'photo_path' => null
        ];
    }

    public function hasItems(int|callable $count = 1): self
    {
        return $this->afterCreating(function ($box) use ($count) {
            $itemCount = is_callable($count) ? $count() : $count;
            Item::factory()
                ->count($itemCount)
                ->create(['box_id' => $box->id]);
        });
    }
}
