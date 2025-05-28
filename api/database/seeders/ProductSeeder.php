<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electroniqueId = Category::where('name', 'Électronique')->first()->id;
        $bureauId = Category::where('name', 'Fournitures de bureau')->first()->id;
        $informatiqueId = Category::where('name', 'Informatique')->first()->id;
        $mobilierId = Category::where('name', 'Mobilier')->first()->id;
        $outillageId = Category::where('name', 'Outillage')->first()->id;

        Product::create([
            'reference' => 'ELEC001',
            'name' => 'Écran LCD 24"',
            'description' => 'Écran haute résolution',
            'category_id' => $electroniqueId,
            'price' => 199.99,
            'quantity' => 15,
            'min_stock' => 5,
        ]);

        Product::create([
            'reference' => 'BUR001',
            'name' => 'Papier A4',
            'description' => 'Ramette 500 feuilles',
            'category_id' => $bureauId,
            'price' => 4.50,
            'quantity' => 50,
            'min_stock' => 10,
        ]);

        Product::create([
            'reference' => 'INFO001',
            'name' => 'Souris optique',
            'description' => 'Souris USB sans fil',
            'category_id' => $informatiqueId,
            'price' => 25.00,
            'quantity' => 30,
            'min_stock' => 8,
        ]);

        Product::create([
            'reference' => 'MOB001',
            'name' => 'Chaise de bureau',
            'description' => 'Chaise ergonomique avec accoudoirs',
            'category_id' => $mobilierId,
            'price' => 149.99,
            'quantity' => 8,
            'min_stock' => 3,
        ]);

        Product::create([
            'reference' => 'OUT001',
            'name' => 'Perceuse sans fil',
            'description' => 'Perceuse 18V avec 2 batteries',
            'category_id' => $outillageId,
            'price' => 129.99,
            'quantity' => 10,
            'min_stock' => 3,
        ]);
    }
}
