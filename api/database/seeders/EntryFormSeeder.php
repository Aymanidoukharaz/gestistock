<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EntryForm;
use App\Models\EntryItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\User;

class EntryFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@gestistock.com')->first();
        $supplier1 = Supplier::where('name', 'TechSupply Inc.')->first();
        $supplier2 = Supplier::where('name', 'Bureau Plus')->first();

        // Vérifier que les utilisateurs et fournisseurs existent
        if (!$admin || !$supplier1 || !$supplier2) {
            echo "Erreur: Utilisateurs ou fournisseurs requis introuvables pour EntryFormSeeder\n";
            return;
        }

        // Bon d'entrée 1
        EntryForm::create([
            'reference' => 'ENT-2024-001',
            'date' => now()->subDays(10),
            'supplier_id' => $supplier1->id,
            'status' => 'completed',
            'total' => 15000.00,
            'user_id' => $admin->id,
        ]);

        // Bon d'entrée 2
        EntryForm::create([
            'reference' => 'ENT-2024-002',
            'date' => now()->subDays(5),
            'supplier_id' => $supplier2->id,
            'status' => 'pending',
            'total' => 8500.50,
            'user_id' => $admin->id,
        ]);

        // Bon d'entrée 3
        EntryForm::create([
            'reference' => 'ENT-2024-003',
            'date' => now()->subDays(2),
            'supplier_id' => $supplier1->id,
            'status' => 'draft',
            'total' => 12000.00,
            'user_id' => $admin->id,
        ]);

        // Bon d'entrée 4
        EntryForm::create([
            'reference' => 'ENT-2024-004',
            'date' => now()->subDay(),
            'supplier_id' => $supplier2->id,
            'status' => 'completed',
            'total' => 6750.75,
            'user_id' => $admin->id,
        ]);

        // Bon d'entrée 5
        EntryForm::create([
            'reference' => 'ENT-2024-005',
            'date' => now(),
            'supplier_id' => $supplier1->id,
            'status' => 'pending',
            'total' => 22500.00,
            'user_id' => $admin->id,
        ]);
    }
}
