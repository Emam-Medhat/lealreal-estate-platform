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
            'price' => $this->faker->numberBetween(100000, 1000000),
            'status' => 'active',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'area' => $this->faker->numberBetween(50, 500),
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'country' => $this->faker->country,
            'property_code' => $this->faker->unique()->numerify('PROP-#####'),
            'area_unit' => 'sqm',
        ];
    }
}
