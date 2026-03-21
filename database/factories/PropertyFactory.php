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
        return [
            'agent_id' => Agent::factory(),
            'title' => $this->faker->sentence(4),
            'slug' => fn (array $attributes) => Str::slug($attributes['title']),
            'property_code' => 'PROP-' . strtoupper(Str::random(8)),
            'description' => $this->faker->paragraph,
            'property_type' => $this->faker->randomElement(['apartment', 'house', 'villa']),
            'listing_type' => $this->faker->randomElement(['sale', 'rent']),
            'price' => $this->faker->numberBetween(100000, 1000000),
            'currency' => 'USD',
            'area' => $this->faker->numberBetween(50, 500),
            'area_unit' => 'sqm',
            'status' => 'active',
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
            'views_count' => 0,
            'inquiries_count' => 0,
            'favorites_count' => 0,
        ];
    }
}
