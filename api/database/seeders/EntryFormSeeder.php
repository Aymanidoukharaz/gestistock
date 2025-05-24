<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntryForm;
use App\Models\EntryItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement;
use Carbon\Carbon;

class EntryFormSeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier s'il existe déjà des bons d'entrée
        $existingEntryForms = EntryForm::all();
        
        if ($existingEntryForms->isEmpty()) {
            // Si aucun bon d'entrée n'existe, créer les bons d'entrée par défaut
            $this->createDefaultEntryForms();
        } else {
            // Sinon, afficher les bons d'entrée existants
            $this->displayExistingEntryForms($existingEntryForms);
        }
    }
    
    /**
     * Créer les bons d'entrée par défaut
     */
    private function createDefaultEntryForms(): void
    {
        // Récupération des données nécessaires
        $admin = User::where('email', 'admin@gestistock.com')->first();
        $techSupplier = Supplier::where('name', 'TechSupply Inc.')->first();
        $bureauSupplier = Supplier::where('name', 'Bureau Plus')->first();
        
        // Produits pour les bons d'entrée
        $ecran = Product::where('reference', 'ELEC001')->first();
        $souris = Product::where('reference', 'ELEC002')->first();
        $papier = Product::where('reference', 'BUR001')->first();
        $stylos = Product::where('reference', 'BUR002')->first();
        
        // Création du premier bon d'entrée
        $entryForm1 = EntryForm::create([
            'reference' => 'ENT-' . date('Ymd') . '-001',
            'date' => Carbon::now()->subDays(15),
            'supplier_id' => $techSupplier->id,
            'notes' => 'Livraison matériel informatique',
            'status' => 'completed',
            'total' => 0, // Sera calculé après l'ajout des items
            'user_id' => $admin->id,
        ]);
        
        // Ajout des items au premier bon d'entrée
        $items1 = [
            [
                'product_id' => $ecran->id,
                'quantity' => 5,
                'unit_price' => 180.00,
                'total' => 5 * 180.00,
            ],
            [
                'product_id' => $souris->id,
                'quantity' => 10,
                'unit_price' => 25.00,
                'total' => 10 * 25.00,
            ],
        ];
        
        $total1 = 0;
        foreach ($items1 as $item) {
            $entryItem = new EntryItem($item);
            $entryForm1->items()->save($entryItem);
            $total1 += $item['total'];
            
            // Création d'un mouvement de stock pour chaque item
            StockMovement::create([
                'product_id' => $item['product_id'],
                'type' => 'entry',
                'quantity' => $item['quantity'],
                'reason' => 'Bon d\'entrée ' . $entryForm1->reference,
                'date' => $entryForm1->date,
                'user_id' => $admin->id,
            ]);
        }
        
        // Mise à jour du total du bon d'entrée
        $entryForm1->update(['total' => $total1]);
        
        // Création du deuxième bon d'entrée
        $entryForm2 = EntryForm::create([
            'reference' => 'ENT-' . date('Ymd') . '-002',
            'date' => Carbon::now()->subDays(7),
            'supplier_id' => $bureauSupplier->id,
            'notes' => 'Réapprovisionnement fournitures de bureau',
            'status' => 'completed',
            'total' => 0, // Sera calculé après l'ajout des items
            'user_id' => $admin->id,
        ]);
        
        // Ajout des items au deuxième bon d'entrée
        $items2 = [
            [
                'product_id' => $papier->id,
                'quantity' => 20,
                'unit_price' => 4.50,
                'total' => 20 * 4.50,
            ],
            [
                'product_id' => $stylos->id,
                'quantity' => 15,
                'unit_price' => 6.80,
                'total' => 15 * 6.80,
            ],
        ];
        
        $total2 = 0;
        foreach ($items2 as $item) {
            $entryItem = new EntryItem($item);
            $entryForm2->items()->save($entryItem);
            $total2 += $item['total'];
            
            // Création d'un mouvement de stock pour chaque item
            StockMovement::create([
                'product_id' => $item['product_id'],
                'type' => 'entry',
                'quantity' => $item['quantity'],
                'reason' => 'Bon d\'entrée ' . $entryForm2->reference,
                'date' => $entryForm2->date,
                'user_id' => $admin->id,
            ]);
        }
          // Mise à jour du total du bon d'entrée
        $entryForm2->update(['total' => $total2]);
        
        echo "Bons d'entrée par défaut créés avec succès.\n";
    }
    
    /**
     * Afficher les bons d'entrée existants
     */
    private function displayExistingEntryForms($entryForms): void
    {
        echo "Utilisation des bons d'entrée existants dans la base de données:\n";
        echo "Nombre total de bons d'entrée: " . $entryForms->count() . "\n";
        
        // Afficher quelques bons à titre d'exemple (pour éviter une liste trop longue)
        $sampleEntryForms = $entryForms->take(3);
        foreach ($sampleEntryForms as $entryForm) {
            echo "- Réf: {$entryForm->reference}, Date: {$entryForm->date->format('Y-m-d')}, Fournisseur: {$entryForm->supplier->name}, Statut: {$entryForm->status}\n";
        }
        
        if ($entryForms->count() > 3) {
            echo "... et " . ($entryForms->count() - 3) . " autres bons d'entrée\n";
        }
    }
}
