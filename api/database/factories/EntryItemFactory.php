<?php

namespace Database\Factories;

use App\Models\EntryForm;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class EntryItemFactory extends Factory
{
    
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
