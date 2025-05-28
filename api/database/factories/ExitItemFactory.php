<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ExitItemFactory extends Factory
{
        public function definition(): array
    {
        return [
            'exit_form_id' => \App\Models\ExitForm::factory(),
            'product_id' => \App\Models\Product::factory(),
            'quantity' => $this->faker->numberBetween(1, 50),
        ];
    }
}
