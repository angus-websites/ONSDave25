<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class LeaveTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('???'),
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'paid' => $this->faker->boolean,
            'core' => $this->faker->boolean,
        ];
    }
}
