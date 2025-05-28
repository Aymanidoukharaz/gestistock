<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ExitForm;
use App\Models\ExitItem;
use App\Models\Product;
use App\Models\User;

class ExitFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@gestistock.com')->first();
        $magasinier = User::where('email', 'magasinier@gestistock.com')->first();

        // Vérifier que les utilisateurs existent
        if (!$admin || !$magasinier) {
            echo "Erreur: Utilisateurs requis introuvables pour ExitFormSeeder\n";
            return;
        }

        // Bon de sortie 1
        ExitForm::create([
            'reference' => 'SOR-2024-001',
            'date' => now()->subDays(8),
            'destination' => 'Bureau Rabat',
            'reason' => 'Équipement bureau',
            'status' => 'completed',
            'user_id' => $admin->id,
        ]);

        // Bon de sortie 2
        ExitForm::create([
            'reference' => 'SOR-2024-002',
            'date' => now()->subDays(4),
            'destination' => 'Agence Casablanca',
            'reason' => 'Matériel informatique',
            'status' => 'pending',
            'user_id' => $magasinier->id,
        ]);

        // Bon de sortie 3
        ExitForm::create([
            'reference' => 'SOR-2024-003',
            'date' => now()->subDays(2),
            'destination' => 'Entrepôt Fès',
            'reason' => 'Transfert stock',
            'status' => 'draft',
            'user_id' => $admin->id,
        ]);

        // Bon de sortie 4
        ExitForm::create([
            'reference' => 'SOR-2024-004',
            'date' => now()->subDay(),
            'destination' => 'Client externe',
            'reason' => 'Vente directe',
            'status' => 'completed',
            'user_id' => $magasinier->id,
        ]);

        // Bon de sortie 5
        ExitForm::create([
            'reference' => 'SOR-2024-005',
            'date' => now(),
            'destination' => 'Maintenance',
            'reason' => 'Réparation',
            'status' => 'pending',
            'user_id' => $admin->id,
        ]);
    }
}
