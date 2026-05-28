<?php

namespace Database\Factories;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    protected $model = Agent::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'is_active' => true,
            'is_verified' => true,
            'hire_date' => now(),
            'license_number' => $this->faker->unique()->bothify('LIC-#######'),
        ];
    }
}
