<?php

namespace Database\Factories;

use App\Models\EntryForm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class EntryFormHistoryFactory extends Factory
{
    
    public function definition(): array
    {
        $fieldNames = ['status', 'reference', 'date', 'notes', 'total'];
        $oldStatuses = ['draft', 'pending', 'completed'];
        $newStatuses = ['pending', 'completed', 'cancelled'];
        
        $fieldName = $this->faker->randomElement($fieldNames);
        
        if ($fieldName === 'status') {
            $oldValue = $this->faker->randomElement($oldStatuses);
            $newValue = $this->faker->randomElement($newStatuses);
        } elseif ($fieldName === 'date') {
            $oldValue = $this->faker->dateTimeBetween('-60 days', '-30 days')->format('Y-m-d');
            $newValue = $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d');
        } elseif ($fieldName === 'total') {
            $oldValue = (string)$this->faker->randomFloat(2, 100, 1000);
            $newValue = (string)$this->faker->randomFloat(2, 1000, 5000);
        } else {
            $oldValue = $this->faker->sentence();
            $newValue = $this->faker->sentence();
        }
        
        return [
            'entry_form_id' => EntryForm::factory(),
            'user_id' => User::factory(),
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'change_reason' => $this->faker->optional(0.8)->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
