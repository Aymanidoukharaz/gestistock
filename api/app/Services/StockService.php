<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class StockService
{
    
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

    
    public function hasEnoughStock(Product $product, int $quantity): bool
    {
        return $product->quantity >= $quantity;
    }

    
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

    
    protected function notifyLowStock(Product $product): void
    {
        Log::warning("Stock faible pour le produit {$product->name} (ID: {$product->id}). " .
                    "Stock actuel: {$product->quantity}, seuil minimum: {$product->min_stock}");
        
        // Ici, on pourrait ajouter plus tard une logique d'envoi d'email ou de notification
    }
}
