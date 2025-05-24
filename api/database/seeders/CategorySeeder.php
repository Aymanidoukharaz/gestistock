<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les catégories existantes
        $existingCategories = Category::all();
        
        if ($existingCategories->isEmpty()) {
            // Si aucune catégorie n'existe, créer les catégories par défaut
            $this->createDefaultCategories();
        } else {
            // Sinon, afficher les catégories existantes
            $this->displayExistingCategories($existingCategories);
        }
    }
    
    /**
     * Créer les catégories par défaut
     */
    private function createDefaultCategories(): void
    {
        $categories = [
            'Électronique',
            'Fournitures de bureau',
            'Mobilier',
            'Informatique',
            'Outillage',
            'Consommables',
            'Produits d\'entretien',
            'Équipement de sécurité',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
        
        echo "Catégories par défaut créées avec succès.\n";
    }
    
    /**
     * Afficher les catégories existantes
     */
    private function displayExistingCategories($categories): void
    {
        echo "Utilisation des catégories existantes dans la base de données:\n";
        foreach ($categories as $category) {
            echo "- {$category->name}\n";
        }
    }
}
