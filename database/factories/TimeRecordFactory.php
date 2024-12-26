<?php

namespace Database\Factories;

use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<TimeRecord>
 */
class TimeRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'recorded_at' => Carbon::now(),
            'type' => $this->faker->randomElement([TimeRecordType::CLOCK_IN, TimeRecordType::CLOCK_OUT]),
            'notes' => $this->faker->sentence,
        ];
    }
}
