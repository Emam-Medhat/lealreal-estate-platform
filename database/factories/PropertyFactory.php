<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);
        return [
            'agent_id' => Agent::factory(),
            'property_code' => 'PROP-' . strtoupper(Str::random(8)),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'property_type' => fake()->randomElement(['apartment', 'villa', 'house', 'land', 'commercial']),
            'listing_type' => fake()->randomElement(['sale', 'rent']),
            'price' => fake()->numberBetween(100000, 5000000),
            'area' => fake()->numberBetween(50, 1000),
            'status' => 'active',
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'views_count' => fake()->numberBetween(0, 1000),
            'inquiries_count' => fake()->numberBetween(0, 100),
            'favorites_count' => fake()->numberBetween(0, 50),
        ];
    }
}
