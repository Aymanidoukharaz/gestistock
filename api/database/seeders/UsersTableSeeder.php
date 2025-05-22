<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Créer un utilisateur magasinier
        User::create([
            'name' => 'Magasinier',
            'email' => 'magasinier@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'magasinier',
        ]);

        // Créer quelques utilisateurs supplémentaires avec le factory (optionnel)
        User::factory()->count(3)->create([
            'role' => 'magasinier',
        ]);
    }
}
