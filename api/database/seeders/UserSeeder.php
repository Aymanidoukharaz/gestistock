<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
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
    }
}
