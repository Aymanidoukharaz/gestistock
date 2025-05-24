<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les produits existants
        $existingProducts = Product::all();
        
        if ($existingProducts->isEmpty()) {
            // Si aucun produit n'existe, créer les produits par défaut
            $this->createDefaultProducts();
        } else {
            // Sinon, afficher les produits existants
            $this->displayExistingProducts($existingProducts);
        }
    }
    
    /**
     * Créer les produits par défaut
     */
    private function createDefaultProducts(): void
    {
        // Récupérer les IDs des catégories
        $electroniqueId = Category::where('name', 'Électronique')->first()->id;
        $bureauId = Category::where('name', 'Fournitures de bureau')->first()->id;
        $mobilierId = Category::where('name', 'Mobilier')->first()->id;
        $informatiqueId = Category::where('name', 'Informatique')->first()->id;
        $outillageId = Category::where('name', 'Outillage')->first()->id;

        // Création des produits
        $products = [
            [
                'reference' => 'ELEC001',
                'name' => 'Écran LCD 24"',
                'description' => 'Écran haute résolution pour ordinateur',
                'category_id' => $electroniqueId,
                'price' => 199.99,
                'quantity' => 15,
                'min_stock' => 5,
            ],
            [
                'reference' => 'ELEC002',
                'name' => 'Souris sans fil',
                'description' => 'Souris ergonomique avec connexion Bluetooth',
                'category_id' => $electroniqueId,
                'price' => 29.99,
                'quantity' => 30,
                'min_stock' => 10,
            ],
            [
                'reference' => 'BUR001',
                'name' => 'Ramette de papier A4',
                'description' => 'Papier blanc 80g/m², 500 feuilles',
                'category_id' => $bureauId,
                'price' => 4.99,
                'quantity' => 50,
                'min_stock' => 15,
            ],
            [
                'reference' => 'BUR002',
                'name' => 'Stylos à bille',
                'description' => 'Lot de 10 stylos à bille, couleur bleue',
                'category_id' => $bureauId,
                'price' => 7.50,
                'quantity' => 40,
                'min_stock' => 20,
            ],
            [
                'reference' => 'MOB001',
                'name' => 'Chaise de bureau',
                'description' => 'Chaise ergonomique avec accoudoirs',
                'category_id' => $mobilierId,
                'price' => 149.99,
                'quantity' => 8,
                'min_stock' => 3,
            ],
            [
                'reference' => 'MOB002',
                'name' => 'Bureau réglable',
                'description' => 'Bureau avec hauteur réglable électriquement',
                'category_id' => $mobilierId,
                'price' => 349.99,
                'quantity' => 5,
                'min_stock' => 2,
            ],
            [
                'reference' => 'INFO001',
                'name' => 'Ordinateur portable',
                'description' => 'Ordinateur portable 15.6", 16GB RAM, 512GB SSD',
                'category_id' => $informatiqueId,
                'price' => 899.99,
                'quantity' => 12,
                'min_stock' => 4,
            ],
            [
                'reference' => 'INFO002',
                'name' => 'Disque dur externe 1TB',
                'description' => 'Disque dur portable USB 3.0',
                'category_id' => $informatiqueId,
                'price' => 69.99,
                'quantity' => 20,
                'min_stock' => 7,
            ],
            [
                'reference' => 'OUT001',
                'name' => 'Perceuse sans fil',
                'description' => 'Perceuse 18V avec 2 batteries',
                'category_id' => $outillageId,
                'price' => 129.99,
                'quantity' => 10,
                'min_stock' => 3,
            ],
            [
                'reference' => 'OUT002',
                'name' => 'Boîte à outils complète',
                'description' => 'Ensemble de 120 outils avec mallette',
                'category_id' => $outillageId,
                'price' => 89.99,
                'quantity' => 7,
                'min_stock' => 2,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
        
        echo "Produits par défaut créés avec succès.\n";
    }
    
    /**
     * Afficher les produits existants
     */
    private function displayExistingProducts($products): void
    {
        echo "Utilisation des produits existants dans la base de données:\n";
        echo "Nombre total de produits: " . $products->count() . "\n";
        
        // Afficher quelques produits à titre d'exemple (pour éviter une liste trop longue)
        $sampleProducts = $products->take(5);
        foreach ($sampleProducts as $product) {
            echo "- {$product->name} (Réf: {$product->reference}), Stock: {$product->quantity}\n";
        }
        
        if ($products->count() > 5) {
            echo "... et " . ($products->count() - 5) . " autres produits\n";
        }
    }
}
