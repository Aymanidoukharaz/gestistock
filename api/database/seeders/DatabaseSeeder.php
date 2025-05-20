<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Exécution des seeders dans un ordre spécifique pour respecter les dépendances
        $this->call([
            // 1. Utilisateurs (nécessaire pour les autres tables)
            UserSeeder::class,
            
            // 2. Tables de base
            CategorySeeder::class,
            SupplierSeeder::class,
            
            // 3. Produits (dépend des catégories)
            ProductSeeder::class,
            
            // 4. Bons d'entrée et de sortie (dépendent des produits, utilisateurs et fournisseurs)
            EntryFormSeeder::class,
            ExitFormSeeder::class,
        ]);
    }
}
