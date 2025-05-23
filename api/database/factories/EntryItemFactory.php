<?php

namespace Database\Factories;

use App\Models\EntryForm;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntryItem>
 */
class EntryItemFactory extends Factory
{
    /**
     * Définir l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 100);
        $unitPrice = $this->faker->randomFloat(2, 5, 500);
        $total = $quantity * $unitPrice;
        
        return [
            'entry_form_id' => EntryForm::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $total,
        ];
    }
}
