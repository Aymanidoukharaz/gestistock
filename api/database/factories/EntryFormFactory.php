<?php

namespace Database\Factories;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntryForm>
 */
class EntryFormFactory extends Factory
{
    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
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
    
    /**
     * Indiquer que le bon d'entrée est en statut brouillon.
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
     * Indiquer que le bon d'entrée est en cours de validation.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }
    
    /**
     * Indiquer que le bon d'entrée est validé.
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
     * Indiquer que le bon d'entrée est annulé.
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
