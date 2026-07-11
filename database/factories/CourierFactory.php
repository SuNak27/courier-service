<?php

namespace Database\Factories;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Courier>
 */
class CourierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->numerify('08##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'vehicle_type' => $this->faker->randomElement(['motorcycle', 'car', 'van', 'bicycle']),
            'vehicle_plate' => $this->faker->bothify('? #### ???'),
            'level' => $this->faker->numberBetween(Courier::MIN_LEVEL, Courier::MAX_LEVEL),
            'is_active' => $this->faker->boolean(90),
        ];
    }

    /**
     * Indicate that the courier has the given level.
     */
    public function level(int $level): static
    {
        return $this->state(fn () => ['level' => $level]);
    }
}
