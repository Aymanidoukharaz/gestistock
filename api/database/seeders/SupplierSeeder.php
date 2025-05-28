<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'name' => 'TechSupply Inc.',
            'email' => 'contact@techsupply.com',
            'phone' => '+212 522 123 456',
            'address' => '123 Avenue Mohammed V, Casablanca'
        ]);

        Supplier::create([
            'name' => 'Bureau Plus',
            'email' => 'info@bureauplus.ma',
            'phone' => '+212 537 987 654',
            'address' => '45 Rue Hassan II, Rabat'
        ]);

        Supplier::create([
            'name' => 'MobilierPro',
            'email' => 'commandes@mobilierpro.com',
            'phone' => '+212 524 456 789',
            'address' => '78 Boulevard Zerktouni, Marrakech'
        ]);

        Supplier::create([
            'name' => 'ElectroMaroc',
            'email' => 'ventes@electromaroc.ma',
            'phone' => '+212 539 112 233',
            'address' => '12 Avenue Al Massira, Fès'
        ]);

        Supplier::create([
            'name' => 'Outillage Expert',
            'email' => 'service@outillage-expert.com',
            'phone' => '+212 528 334 455',
            'address' => '56 Rue Ibn Sina, Agadir'
        ]);
    }
}
