<?php

namespace Database\Seeders;

use App\Models\EntryForm;
use App\Models\EntryFormHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class EntryFormHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entryForms = EntryForm::all();
        $users = User::all();

        if ($entryForms->isEmpty() || $users->isEmpty()) {
            return;
        }

        // Créer 5 historiques d'entrée réalistes
        $histories = [
            [
                'entry_form_id' => $entryForms->first()->id,
                'user_id' => $users->where('role', 'admin')->first()->id,
                'field_name' => 'status',
                'old_value' => null,
                'new_value' => 'draft',
                'change_reason' => 'Création du bon d\'entrée'
            ],
            [
                'entry_form_id' => $entryForms->first()->id,
                'user_id' => $users->where('role', 'admin')->first()->id,
                'field_name' => 'status',
                'old_value' => 'draft',
                'new_value' => 'pending',
                'change_reason' => 'Soumission pour validation'
            ],
            [
                'entry_form_id' => $entryForms->skip(1)->first()->id,
                'user_id' => $users->where('role', 'magasinier')->first()->id,
                'field_name' => 'total_amount',
                'old_value' => '15000',
                'new_value' => '16500',
                'change_reason' => 'Correction du montant total'
            ],
            [
                'entry_form_id' => $entryForms->skip(2)->first()->id,
                'user_id' => $users->where('role', 'admin')->first()->id,
                'field_name' => 'status',
                'old_value' => 'pending',
                'new_value' => 'completed',
                'change_reason' => 'Validation et approbation finale'
            ],
            [
                'entry_form_id' => $entryForms->skip(3)->first()->id,
                'user_id' => $users->where('role', 'magasinier')->first()->id,
                'field_name' => 'supplier_id',
                'old_value' => '1',
                'new_value' => '2',
                'change_reason' => 'Changement de fournisseur'
            ]
        ];

        foreach ($histories as $history) {
            EntryFormHistory::create($history);
        }
    }
}
