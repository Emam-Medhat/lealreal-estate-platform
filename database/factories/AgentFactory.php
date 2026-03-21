<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    protected $model = Agent::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'license_number' => 'LIC-' . $this->faker->unique()->numberBetween(10000, 99999),
            'hire_date' => now(),
            'is_active' => true,
            'is_verified' => true,
        ];
    }
}
