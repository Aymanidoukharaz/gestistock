<?php
// Debug script for entry forms and items

namespace App\Debug;

use App\Models\EntryForm;
use App\Models\EntryItem;
use Illuminate\Support\Facades\DB;

class EntryFormDebug
{
    public static function testCreate()
    {
        try {
            DB::beginTransaction();
            
            // Créer un bon d'entrée de test
            $entry = EntryForm::create([
                'reference' => 'DEBUG-' . time(),
                'date' => now(),
                'supplier_id' => 1, // Assurez-vous que ce fournisseur existe
                'notes' => 'Test de débogage',
                'status' => 'draft',
                'user_id' => 1, // Assurez-vous que cet utilisateur existe
                'total' => 0
            ]);
            
            echo "Bon d'entrée créé avec ID: " . $entry->id . "\n";
            
            // Créer un item de test
            $item = new EntryItem([
                'product_id' => 1, // Assurez-vous que ce produit existe
                'quantity' => 5,
                'unit_price' => 10.00,
                'total' => 50.00
            ]);
            
            // Associer l'item au bon d'entrée
            $result = $entry->items()->save($item);
            
            echo "Item créé avec ID: " . ($result ? $result->id : "Échec") . "\n";
            
            // Vérifier que l'item a bien été associé
            $entry->refresh();
            $itemCount = $entry->items()->count();
            echo "Nombre d'items associés: " . $itemCount . "\n";
            
            DB::commit();
            echo "Transaction validée avec succès\n";
            
            // Vérification finale
            $entryWithItems = EntryForm::with('items')->find($entry->id);
            echo "Vérification - nombre d'items: " . $entryWithItems->items->count() . "\n";
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            echo "ERREUR: " . $e->getMessage() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
            return false;
        }
    }
}

// Exécuter le test
echo "Début du test de débogage\n";
\App\Debug\EntryFormDebug::testCreate();
echo "Fin du test de débogage\n";
