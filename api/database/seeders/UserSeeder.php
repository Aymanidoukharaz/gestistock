<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier s'il existe déjà des utilisateurs
        $existingUsers = User::all();
        
        if ($existingUsers->isEmpty()) {
            // Si aucun utilisateur n'existe, créer les utilisateurs par défaut
            $this->createDefaultUsers();
        } else {
            // Sinon, afficher les utilisateurs existants
            $this->displayExistingUsers($existingUsers);
        }
    }
    
    /**
     * Créer les utilisateurs par défaut
     */
    private function createDefaultUsers(): void
    {
        // Création d'un utilisateur admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);

        // Création d'un utilisateur magasinier
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
        echo "Nombre total d'utilisateurs: " . $users->count() . "\n";
        
        foreach ($users as $user) {
            $status = $user->active ? 'Actif' : 'Inactif';
            echo "- {$user->name} ({$user->email}) - Rôle: {$user->role} - Statut: {$status}\n";
        }
    }
}
