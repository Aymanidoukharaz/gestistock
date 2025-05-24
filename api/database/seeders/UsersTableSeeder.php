<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les utilisateurs existants de la base de données
        $users = User::all();
        
        // Vérifier si nous avons des utilisateurs dans la base de données
        if ($users->isEmpty()) {
            // Si aucun utilisateur n'existe, créer les utilisateurs par défaut
            $this->createDefaultUsers();
        } else {
            // Sinon, afficher les utilisateurs existants
            $this->displayExistingUsers($users);
        }
    }
    
    /**
     * Créer les utilisateurs par défaut
     */
    private function createDefaultUsers(): void
    {
        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);

        // Créer un utilisateur magasinier
        User::create([
            'name' => 'Magasinier',
            'email' => 'magasinier@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'magasinier',
            'active' => true,
        ]);

        echo "Utilisateurs par défaut créés avec succès.\n";
    }
    
    /**
     * Afficher les utilisateurs existants
     */
    private function displayExistingUsers($users): void
    {
        echo "Utilisation des utilisateurs existants dans la base de données:\n";
        foreach ($users as $user) {
            echo "- {$user->name} ({$user->email}), Rôle: {$user->role}\n";
        }
    }
}
