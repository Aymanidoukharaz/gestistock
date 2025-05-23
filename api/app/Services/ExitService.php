<?php

namespace App\Services;

use App\Models\ExitForm;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExitService
{
    protected $stockService;

    /**
     * Constructeur du service.
     *
     * @param StockService $stockService
     */
    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Valider un bon de sortie et mettre à jour le stock.
     *
     * @param ExitForm $exitForm Bon de sortie à valider.
     * @return ExitForm Le bon de sortie validé.
     * @throws Exception Si le bon de sortie n'est pas en statut draft, 
     *                   si le stock est insuffisant ou en cas d'erreur.
     */
    public function validate(ExitForm $exitForm): ExitForm
    {
        // Vérifier si le bon est en statut draft
        if ($exitForm->status !== 'draft') {
            throw new Exception("Le bon de sortie doit être en statut 'draft' pour être validé.");
        }

        // Transaction pour assurer l'intégrité des données
        return DB::transaction(function () use ($exitForm) {
            try {
                // Étape 1: Vérifier la disponibilité du stock pour tous les produits
                foreach ($exitForm->exitItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    if (!$this->stockService->hasEnoughStock($product, $item->quantity)) {
                        throw new Exception(
                            "Stock insuffisant pour le produit {$product->name}. " .
                            "Disponible: {$product->quantity}, Demandé: {$item->quantity}"
                        );
                    }
                }                // Étape 2: Changer le statut en 'pending'
                $exitForm->status = 'pending';
                $exitForm->save();
                
                // Étape 3: Traiter chaque ligne du bon de sortie
                foreach ($exitForm->exitItems as $item) {
                    $product = Product::findOrFail($item->product_id);
                    
                    // Soustraire la quantité du stock (quantité négative)
                    $this->stockService->updateStock($product, -$item->quantity);
                    
                    // Créer un mouvement de stock pour cette sortie
                    $this->stockService->createStockMovement(
                        $product,
                        'exit',
                        $item->quantity,
                        "Sortie de stock via bon #" . $exitForm->reference
                    );
                }

                // Étape 4: Changer le statut en 'completed'
                $exitForm->status = 'completed';
                $exitForm->save();

                Log::info("Bon de sortie #{$exitForm->reference} validé avec succès");
                return $exitForm;
            } catch (Exception $e) {
                Log::error("Erreur lors de la validation du bon de sortie #{$exitForm->reference}: " . $e->getMessage());
                throw $e;
            }
        });
    }
}
