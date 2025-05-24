<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExitForm;
use App\Models\ExitItem;
use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement;
use Carbon\Carbon;

class ExitFormSeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier s'il existe déjà des bons de sortie
        $existingExitForms = ExitForm::all();
        
        if ($existingExitForms->isEmpty()) {
            // Si aucun bon de sortie n'existe, créer les bons de sortie par défaut
            $this->createDefaultExitForms();
        } else {
            // Sinon, afficher les bons de sortie existants
            $this->displayExistingExitForms($existingExitForms);
        }
    }
    
    /**
     * Créer les bons de sortie par défaut
     */
    private function createDefaultExitForms(): void
    {
        // Récupération des données nécessaires
        $admin = User::where('email', 'admin@gestistock.com')->first();
        $magasinier = User::where('email', 'magasinier@gestistock.com')->first();
        
        // Produits pour les bons de sortie
        $ecran = Product::where('reference', 'ELEC001')->first();
        $souris = Product::where('reference', 'ELEC002')->first();
        $papier = Product::where('reference', 'BUR001')->first();
        $stylos = Product::where('reference', 'BUR002')->first();
        
        // Création du premier bon de sortie
        $exitForm1 = ExitForm::create([
            'reference' => 'SOR-' . date('Ymd') . '-001',
            'date' => Carbon::now()->subDays(10),
            'destination' => 'Service Comptabilité',
            'reason' => 'Équipement des nouveaux postes de travail',
            'notes' => 'Demande urgente approuvée par la direction',
            'status' => 'completed',
            'user_id' => $admin->id,
        ]);
        
        // Ajout des items au premier bon de sortie
        $items1 = [
            [
                'product_id' => $ecran->id,
                'quantity' => 2,
            ],
            [
                'product_id' => $souris->id,
                'quantity' => 4,
            ],
        ];
        
        foreach ($items1 as $item) {
            $exitItem = new ExitItem($item);
            $exitForm1->items()->save($exitItem);
            
            // Création d'un mouvement de stock pour chaque item
            StockMovement::create([
                'product_id' => $item['product_id'],
                'type' => 'exit',
                'quantity' => $item['quantity'],
                'reason' => 'Bon de sortie ' . $exitForm1->reference,
                'date' => $exitForm1->date,
                'user_id' => $admin->id,
            ]);
        }
        
        // Création du deuxième bon de sortie
        $exitForm2 = ExitForm::create([
            'reference' => 'SOR-' . date('Ymd') . '-002',
            'date' => Carbon::now()->subDays(3),
            'destination' => 'Service Marketing',
            'reason' => 'Réapprovisionnement mensuel',
            'notes' => null,
            'status' => 'completed',
            'user_id' => $magasinier->id,
        ]);
        
        // Ajout des items au deuxième bon de sortie
        $items2 = [
            [
                'product_id' => $papier->id,
                'quantity' => 5,
            ],
            [
                'product_id' => $stylos->id,
                'quantity' => 8,
            ],
        ];
        
        foreach ($items2 as $item) {
            $exitItem = new ExitItem($item);
            $exitForm2->items()->save($exitItem);
            
            // Création d'un mouvement de stock pour chaque item
            StockMovement::create([
                'product_id' => $item['product_id'],
                'type' => 'exit',
                'quantity' => $item['quantity'],
                'reason' => 'Bon de sortie ' . $exitForm2->reference,
                'date' => $exitForm2->date,
                'user_id' => $magasinier->id,
            ]);
        }
        
        echo "Bons de sortie par défaut créés avec succès.\n";
    }
    
    /**
     * Afficher les bons de sortie existants
     */
    private function displayExistingExitForms($exitForms): void
    {
        echo "Utilisation des bons de sortie existants dans la base de données:\n";
        echo "Nombre total de bons de sortie: " . $exitForms->count() . "\n";
        
        // Afficher quelques bons à titre d'exemple (pour éviter une liste trop longue)
        $sampleExitForms = $exitForms->take(3);
        foreach ($sampleExitForms as $exitForm) {
            echo "- Réf: {$exitForm->reference}, Date: {$exitForm->date->format('Y-m-d')}, Destination: {$exitForm->destination}, Statut: {$exitForm->status}\n";
        }
        
        if ($exitForms->count() > 3) {
            echo "... et " . ($exitForms->count() - 3) . " autres bons de sortie\n";
        }
    }
}
