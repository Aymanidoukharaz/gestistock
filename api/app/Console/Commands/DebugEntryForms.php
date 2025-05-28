<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EntryForm;
use App\Models\EntryItem;
use Illuminate\Support\Facades\DB;

class DebugEntryForms extends Command
{
    
    protected $signature = 'debug:entry-forms';

    
    protected $description = 'Debug entry forms and items creation';

    
    public function handle()
    {
        $this->info('Démarrage du test de création de bon d\'entrée et items');

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
            
            $this->info('Bon d\'entrée créé avec ID: ' . $entry->id);
            
            // Créer un item de test
            $item = new EntryItem([
                'product_id' => 1, // Assurez-vous que ce produit existe
                'quantity' => 5,
                'unit_price' => 10.00,
                'total' => 50.00
            ]);
            
            // Associer l'item au bon d'entrée
            $result = $entry->items()->save($item);
            
            $this->info('Item créé avec ID: ' . ($result ? $result->id : 'Échec'));
            
            // Vérifier que l'item a bien été associé
            $entry->refresh();
            $itemCount = $entry->items()->count();
            $this->info('Nombre d\'items associés: ' . $itemCount);
            
            DB::commit();
            $this->info('Transaction validée avec succès');
            
            // Vérification finale
            $entryWithItems = EntryForm::with('items')->find($entry->id);
            $this->info('Vérification finale - nombre d\'items: ' . $entryWithItems->items->count());

            if ($entryWithItems->items->count() > 0) {
                $this->table(
                    ['ID', 'Product ID', 'Quantity', 'Unit Price', 'Total'],
                    $entryWithItems->items->map(fn($item) => [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->total,
                    ])
                );
            }

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('ERREUR: ' . $e->getMessage());
            $this->error('Type: ' . get_class($e));
            $this->error('Fichier: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}
