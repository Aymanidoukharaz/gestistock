<?php

namespace Database\Seeders;

use App\Models\EntryForm;
use App\Models\EntryFormHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EntryFormHistorySeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier s'il existe déjà des historiques de bons d'entrée
        $existingHistory = EntryFormHistory::all();
        
        if ($existingHistory->isEmpty()) {
            // Si aucun historique n'existe, créer les historiques par défaut
            $this->createDefaultEntryFormHistory();
        } else {
            // Sinon, afficher les historiques existants
            $this->displayExistingEntryFormHistory($existingHistory);
        }
    }
    
    /**
     * Créer les historiques de bons d'entrée par défaut
     */
    private function createDefaultEntryFormHistory(): void
    {
        // Récupération des bons d'entrée existants
        $entryForms = EntryForm::all();
        $admin = User::where('role', 'admin')->first();

        if ($entryForms->isEmpty() || !$admin) {
            echo "Aucun bon d'entrée ou utilisateur admin trouvé. Historiques non créés.\n";
            return;
        }

        foreach ($entryForms as $entryForm) {
            // Créer un historique de création
            EntryFormHistory::create([
                'entry_form_id' => $entryForm->id,
                'user_id' => $admin->id,
                'field_name' => 'status',
                'old_value' => null,
                'new_value' => 'draft',
                'change_reason' => 'Création du bon d\'entrée',
                'created_at' => Carbon::parse($entryForm->created_at)->subHours(2),
                'updated_at' => Carbon::parse($entryForm->created_at)->subHours(2),
            ]);

            // Si le bon est complété, créer un historique de validation
            if ($entryForm->status === 'completed') {
                EntryFormHistory::create([
                    'entry_form_id' => $entryForm->id,
                    'user_id' => $admin->id,
                    'field_name' => 'status',
                    'old_value' => 'draft',
                    'new_value' => 'pending',
                    'change_reason' => 'Début du processus de validation',
                    'created_at' => Carbon::parse($entryForm->created_at)->subHour(),
                    'updated_at' => Carbon::parse($entryForm->created_at)->subHour(),
                ]);

                EntryFormHistory::create([
                    'entry_form_id' => $entryForm->id,
                    'user_id' => $admin->id,
                    'field_name' => 'status',
                    'old_value' => 'pending',
                    'new_value' => 'completed',
                    'change_reason' => 'Validation complète du bon d\'entrée',
                    'created_at' => Carbon::parse($entryForm->updated_at),
                    'updated_at' => Carbon::parse($entryForm->updated_at),
                ]);
            }
            
            // Si le bon est annulé, créer un historique d'annulation
            if ($entryForm->status === 'cancelled') {
                $previousStatus = rand(0, 1) ? 'draft' : 'completed';
                EntryFormHistory::create([
                    'entry_form_id' => $entryForm->id,
                    'user_id' => $admin->id,
                    'field_name' => 'status',
                    'old_value' => $previousStatus,
                    'new_value' => 'cancelled',
                    'change_reason' => 'Annulation du bon d\'entrée',
                    'created_at' => Carbon::parse($entryForm->updated_at),
                    'updated_at' => Carbon::parse($entryForm->updated_at),
                ]);
            }
        }
        
        echo "Historiques de bons d'entrée par défaut créés avec succès.\n";
    }
    
    /**
     * Afficher les historiques de bons d'entrée existants
     */
    private function displayExistingEntryFormHistory($histories): void
    {
        echo "Utilisation des historiques de bons d'entrée existants dans la base de données:\n";
        echo "Nombre total d'historiques: " . $histories->count() . "\n";
        
        // Regrouper par bon d'entrée
        $historiesByForm = $histories->groupBy('entry_form_id');
        echo "Répartition par bon d'entrée:\n";
        foreach ($historiesByForm as $formId => $formHistories) {
            $entryForm = EntryForm::find($formId);
            if ($entryForm) {
                echo "- Bon {$entryForm->reference}: {$formHistories->count()} entrées d'historique\n";
            }
        }
    }
}
