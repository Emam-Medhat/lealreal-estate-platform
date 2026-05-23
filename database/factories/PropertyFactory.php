<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        return [
            'agent_id' => Agent::factory(),
            'property_code' => $this->faker->unique()->word,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'property_type' => $this->faker->randomElement(['apartment', 'villa', 'house', 'land', 'commercial']),
            'listing_type' => $this->faker->randomElement(['sale', 'rent']),
            'price' => $this->faker->randomFloat(2, 100000, 1000000),
            'area' => $this->faker->randomFloat(2, 50, 500),
            'area_unit' => 'sq_m',
            'status' => 'active',
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'country' => $this->faker->country,
        ];
    }
}
