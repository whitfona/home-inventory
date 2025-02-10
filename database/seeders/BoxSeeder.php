<?php

namespace Database\Seeders;

use App\Models\Box;
use App\Models\User;
use Illuminate\Database\Seeder;

class BoxSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()
            ->create([
                'name' => 'Nick',
                'email' => 'nick@example.com',
                'password' => bcrypt('password'),
            ]);

        Box::factory()
            ->count(100)
            ->hasItems(fn() => fake()->numberBetween(0, 10))
            ->create();
    }
}
