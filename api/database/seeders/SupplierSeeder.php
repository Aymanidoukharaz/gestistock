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
        $suppliers = [
            [
                'name' => 'TechSupply Inc.',
                'email' => 'contact@techsupply.com',
                'phone' => '+212 522 123 456',
                'address' => '123 Avenue de la Technologie, Casablanca',
                'notes' => 'Fournisseur de matériel informatique et électronique',
            ],
            [
                'name' => 'Bureau Plus',
                'email' => 'info@bureauplus.ma',
                'phone' => '+212 537 987 654',
                'address' => '45 Rue des Papeteries, Rabat',
                'notes' => 'Spécialiste en fournitures de bureau',
            ],
            [
                'name' => 'MobilierPro',
                'email' => 'commandes@mobilierpro.com',
                'phone' => '+212 535 456 789',
                'address' => '78 Zone Industrielle, Fès',
                'notes' => 'Fabricant et distributeur de mobilier de bureau',
            ],
            [
                'name' => 'OutilExpert',
                'email' => 'service@outilexpert.ma',
                'phone' => '+212 528 234 567',
                'address' => '56 Boulevard Industriel, Agadir',
                'notes' => 'Distributeur d\'outillage professionnel',
            ],
            [
                'name' => 'CleanPro',
                'email' => 'ventes@cleanpro.ma',
                'phone' => '+212 539 345 678',
                'address' => '34 Avenue Hassan II, Tanger',
                'notes' => 'Produits d\'entretien et de nettoyage',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
