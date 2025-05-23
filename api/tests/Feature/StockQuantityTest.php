<?php

namespace Tests\Feature;

use App\Models\EntryForm;
use App\Models\EntryItem;
use App\Models\ExitForm;
use App\Models\ExitItem;
use App\Models\Product;
use App\Models\User;
use App\Services\EntryService;
use App\Services\ExitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StockQuantityTest extends TestCase
{
    use RefreshDatabase;
    
    protected $entryService;
    protected $exitService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->entryService = app(EntryService::class);
        $this->exitService = app(ExitService::class);
    }
    
    /** @test */
    public function test_quantity_update_on_entry_form_validation()
    {
        // Créer un utilisateur
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        // Créer un produit avec un stock initial
        $product = Product::factory()->create([
            'name' => 'Produit test entrée',
            'quantity' => 10,
            'price' => 100.00,
        ]);
        
        $initialQuantity = $product->quantity;
        
        // Créer un bon d'entrée
        $entry = EntryForm::factory()->create([
            'reference' => 'TEST-ENTRY-' . time(),
            'status' => 'draft',
            'user_id' => $user->id,
            'supplier_id' => 1, // Assurez-vous que ce fournisseur existe
        ]);
        
        // Quantité à ajouter
        $quantityToAdd = 5;
        
        // Ajouter un item au bon d'entrée
        EntryItem::factory()->create([
            'entry_form_id' => $entry->id,
            'product_id' => $product->id,
            'quantity' => $quantityToAdd,
            'unit_price' => 120.00,
            'total' => 600.00
        ]);
        
        // Valider le bon d'entrée
        $this->entryService->validate($entry->fresh());
        
        // Récupérer le produit mis à jour
        $product->refresh();
        
        // Vérifier que la quantité a été augmentée
        $this->assertEquals($initialQuantity + $quantityToAdd, $product->quantity);
        
        echo "Test d'entrée réussi : Quantité mise à jour de {$initialQuantity} à {$product->quantity}\n";
    }
    
    /** @test */
    public function test_quantity_update_on_exit_form_validation()
    {
        // Créer un utilisateur
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        // Créer un produit avec un stock initial suffisant
        $product = Product::factory()->create([
            'name' => 'Produit test sortie',
            'quantity' => 20,
            'price' => 100.00,
        ]);
        
        $initialQuantity = $product->quantity;
        
        // Créer un bon de sortie
        $exit = ExitForm::factory()->create([
            'reference' => 'TEST-EXIT-' . time(),
            'status' => 'draft',
            'user_id' => $user->id,
            'destination' => 'Test Department',
        ]);
        
        // Quantité à soustraire
        $quantityToRemove = 8;
        
        // Ajouter un item au bon de sortie
        ExitItem::factory()->create([
            'exit_form_id' => $exit->id,
            'product_id' => $product->id,
            'quantity' => $quantityToRemove
        ]);
        
        // Valider le bon de sortie
        $this->exitService->validate($exit->fresh());
        
        // Récupérer le produit mis à jour
        $product->refresh();
        
        // Vérifier que la quantité a été diminuée
        $this->assertEquals($initialQuantity - $quantityToRemove, $product->quantity);
        
        echo "Test de sortie réussi : Quantité mise à jour de {$initialQuantity} à {$product->quantity}\n";
    }
}
