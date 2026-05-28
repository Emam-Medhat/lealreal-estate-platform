<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition()
    {
        return [
            'agent_id' => Agent::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'price' => $this->faker->numberBetween(100000, 1000000),
            'area' => $this->faker->numberBetween(50, 500),
            'status' => 'active',
            'city' => $this->faker->city,
            'address' => $this->faker->address,
            'property_code' => $this->faker->unique()->bothify('PROP-########'),
            'country' => $this->faker->country,
            'state' => $this->faker->state,
            'area_unit' => 'sq_m',
        ];
    }
}
