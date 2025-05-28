<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ExitFormFactory extends Factory
{
        public function definition(): array
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
    
    
    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft',
            ];
        });
    }
    
    
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }
    
    
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }
}
