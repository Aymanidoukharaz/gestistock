<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class EntryFormFactory extends Factory
{
    
    public function definition(): array
    {
        $statuses = ['draft', 'pending', 'completed', 'cancelled'];
        
        return [
            'reference' => 'ENT-' . $this->faker->unique()->numberBetween(1000, 9999),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'supplier_id' => Supplier::factory(),
            'notes' => $this->faker->optional(0.7)->sentence(),
            'status' => $this->faker->randomElement($statuses),
            'total' => $this->faker->randomFloat(2, 100, 5000),
            'user_id' => User::factory(),
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
    
    
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
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
