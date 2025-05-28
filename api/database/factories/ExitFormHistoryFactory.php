<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


class ExitFormHistoryFactory extends Factory
{
        public function definition(): array
    {
        return [
            'exit_form_id' => \App\Models\ExitForm::factory(),
            'user_id' => \App\Models\User::factory(),
            'field_name' => $this->faker->randomElement(['status', 'destination', 'reason', 'notes']),
            'old_value' => $this->faker->word(),
            'new_value' => $this->faker->word(),
            'change_reason' => $this->faker->sentence(),
        ];
    }
}
