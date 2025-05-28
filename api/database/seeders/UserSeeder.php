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
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);

        User::create([
            'name' => 'Magasinier',
            'email' => 'magasinier@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'magasinier',
            'active' => true,
        ]);

        User::create([
            'name' => 'Ahmed Ben Ali',
            'email' => 'ahmed@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'active' => true,
        ]);

        User::create([
            'name' => 'Fatima Zahra',
            'email' => 'fatima@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'magasinier',
            'active' => true,
        ]);

        User::create([
            'name' => 'Youssef Alami',
            'email' => 'youssef@gestistock.com',
            'password' => Hash::make('password'),
            'role' => 'magasinier',
            'active' => false,
        ]);
    }
}
