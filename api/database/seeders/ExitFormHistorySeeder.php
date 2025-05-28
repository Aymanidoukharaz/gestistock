<?php

namespace Database\Seeders;

use App\Models\ExitForm;
use App\Models\ExitFormHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExitFormHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $exitForms = ExitForm::all();
        $users = User::all();

        if ($exitForms->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Créer 5 historiques de sortie réalistes
        $histories = [
            [
                'exit_form_id' => $exitForms->first()->id,
                'user_id' => $users->where('role', 'admin')->first()->id,
                'field_name' => 'status',
                'old_value' => null,
                'new_value' => 'draft',
                'change_reason' => 'Création du bon de sortie'
            ],
            [
                'exit_form_id' => $exitForms->first()->id,
                'user_id' => $users->where('role', 'magasinier')->first()->id,
                'field_name' => 'destination',
                'old_value' => 'Bureau principal',
                'new_value' => 'Succursale Rabat',
                'change_reason' => 'Changement de destination'
            ],
            [
                'exit_form_id' => $exitForms->skip(1)->first()->id,
                'user_id' => $users->where('role', 'admin')->first()->id,
                'field_name' => 'status',
                'old_value' => 'draft',
                'new_value' => 'completed',
                'change_reason' => 'Validation et autorisation de sortie'
            ],
            [
                'exit_form_id' => $exitForms->skip(2)->first()->id,
                'user_id' => $users->where('role', 'magasinier')->first()->id,
                'field_name' => 'reason',
                'old_value' => 'Réparation',
                'new_value' => 'Maintenance préventive',
                'change_reason' => 'Précision de la raison'
            ],
            [
                'exit_form_id' => $exitForms->skip(3)->first()->id,
                'user_id' => $users->where('role', 'admin')->first()->id,
                'field_name' => 'status',
                'old_value' => 'pending',
                'new_value' => 'cancelled',
                'change_reason' => 'Annulation de la demande'
            ]
        ];

        foreach ($histories as $history) {
            ExitFormHistory::create($history);
        }
    }
}
