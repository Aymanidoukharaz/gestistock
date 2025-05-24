<?php

namespace Database\Seeders;

use App\Models\ExitForm;
use App\Models\ExitFormHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExitFormHistorySeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier s'il existe déjà des historiques de bons de sortie
        $existingHistory = ExitFormHistory::all();
        
        if ($existingHistory->isEmpty()) {
            // Si aucun historique n'existe, créer les historiques par défaut
            $this->createDefaultExitFormHistory();
        } else {
            // Sinon, afficher les historiques existants
            $this->displayExistingExitFormHistory($existingHistory);
        }
    }
    
    /**
     * Créer les historiques de bons de sortie par défaut
     */
    private function createDefaultExitFormHistory(): void
    {
        // Récupérer tous les bons de sortie
        $exitForms = ExitForm::all();
        $users = User::all();

        if ($exitForms->isEmpty() || $users->isEmpty()) {
            echo "Aucun bon de sortie ou utilisateur trouvé. Historiques non créés.\n";
            return;
        }

        // Pour chaque bon, créer quelques entrées d'historique
        foreach ($exitForms as $exitForm) {
            // Créer 1 à 5 entrées d'historique par bon
            $historyCount = rand(1, 5);
            
            for ($i = 0; $i < $historyCount; $i++) {
                ExitFormHistory::factory()->create([
                    'exit_form_id' => $exitForm->id,
                    'user_id' => $users->random()->id
                ]);
            }
        }
        
        echo "Historiques de bons de sortie par défaut créés avec succès.\n";
    }
    
    /**
     * Afficher les historiques de bons de sortie existants
     */
    private function displayExistingExitFormHistory($histories): void
    {
        echo "Utilisation des historiques de bons de sortie existants dans la base de données:\n";
        echo "Nombre total d'historiques: " . $histories->count() . "\n";
        
        // Regrouper par bon de sortie
        $historiesByForm = $histories->groupBy('exit_form_id');
        echo "Répartition par bon de sortie:\n";
        foreach ($historiesByForm as $formId => $formHistories) {
            $exitForm = ExitForm::find($formId);
            if ($exitForm) {
                echo "- Bon {$exitForm->reference}: {$formHistories->count()} entrées d'historique\n";
            }
        }
    }
}
