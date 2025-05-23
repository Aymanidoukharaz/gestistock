<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class StockService
{
    /**
     * Mettre à jour le stock d'un produit (augmenter/diminuer).
     *
     * @param Product $product Le produit à mettre à jour.
     * @param int $quantity La quantité à ajouter (positif) ou soustraire (négatif).
     * @param float|null $newPrice Nouveau prix du produit (optionnel, pour les entrées).
     * @return Product Le produit mis à jour.
     */
    public function updateStock(Product $product, int $quantity, float $newPrice = null): Product
    {
        // Mise à jour de la quantité
        $product->quantity += $quantity;

        // Si une nouvelle valeur de prix est fournie, on met à jour le prix
        if ($newPrice !== null && $quantity > 0) {
            $product->price = $newPrice;
        }

        $product->save();

        // Vérifier si le stock est inférieur au seuil minimum
        if ($product->quantity < $product->min_stock) {
            $this->notifyLowStock($product);
        }

        return $product;
    }

    /**
     * Vérifier si un produit a suffisamment de stock pour une sortie.
     *
     * @param Product $product Le produit à vérifier.
     * @param int $quantity La quantité demandée.
     * @return bool True si le stock est suffisant, false sinon.
     */
    public function hasEnoughStock(Product $product, int $quantity): bool
    {
        return $product->quantity >= $quantity;
    }

    /**
     * Créer un mouvement de stock.
     *
     * @param Product $product Le produit concerné.
     * @param string $type Le type de mouvement ('entry' ou 'exit').
     * @param int $quantity La quantité concernée.
     * @param string $reason La raison du mouvement.
     * @return StockMovement Le mouvement de stock créé.
     */
    public function createStockMovement(Product $product, string $type, int $quantity, string $reason): StockMovement
    {
        return StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'reason' => $reason,
            'date' => now(),
            'user_id' => Auth::id(),
        ]);
    }

    /**
     * Notifier d'un stock faible.
     * Note: Dans un système réel, cette méthode pourrait envoyer des emails ou des notifications.
     *
     * @param Product $product Le produit en stock faible.
     * @return void
     */
    protected function notifyLowStock(Product $product): void
    {
        Log::warning("Stock faible pour le produit {$product->name} (ID: {$product->id}). " .
                    "Stock actuel: {$product->quantity}, seuil minimum: {$product->min_stock}");
        
        // Ici, on pourrait ajouter plus tard une logique d'envoi d'email ou de notification
    }
}
