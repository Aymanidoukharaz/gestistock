<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExitForm>
 */
class ExitFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */    public function definition(): array
    {
        return [
            'reference' => 'BS-' . $this->faker->unique()->numerify('######'),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'destination' => $this->faker->company(),
            'reason' => $this->faker->sentence(),
            'notes' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['draft', 'pending', 'completed', 'cancelled']),
            'user_id' => \App\Models\User::factory(),
        ];
    }
    
    /**
     * Indicate that the exit form is in draft status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
            ];
        });
    }
    
    /**
     * Indicate that the exit form is in completed status.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }
    
    /**
     * Indicate that the exit form is cancelled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
}
