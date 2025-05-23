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

class StockManagementTest extends TestCase
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
    public function it_can_validate_an_entry_form_and_update_stock()
    {
        // Créer un utilisateur
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        // Créer un produit avec un stock initial
        $product = Product::factory()->create([
            'quantity' => 10,
            'price' => 100.00,
            'min_stock' => 5
        ]);
        
        // Créer un bon d'entrée
        $entry = EntryForm::factory()->create([
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
        
        // Ajouter un item au bon d'entrée
        EntryItem::factory()->create([
            'entry_form_id' => $entry->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 120.00,
            'total' => 600.00
        ]);
        
        // Valider le bon d'entrée
        $validatedEntry = $this->entryService->validate($entry->fresh());
        
        // Vérifier que le statut a été mis à jour
        $this->assertEquals('completed', $validatedEntry->status);
        
        // Vérifier que la quantité en stock a été mise à jour
        $product->refresh();
        $this->assertEquals(15, $product->quantity);
        
        // Vérifier que le prix a été mis à jour
        $this->assertEquals(120.00, $product->price);
        
        // Vérifier qu'un mouvement de stock a été créé
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 5,
            'user_id' => $user->id
        ]);
    }
    
    /** @test */
    public function it_can_validate_an_exit_form_and_update_stock()
    {
        // Créer un utilisateur
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        // Créer un produit avec un stock initial
        $product = Product::factory()->create([
            'quantity' => 10,
            'price' => 100.00,
            'min_stock' => 5
        ]);
        
        // Créer un bon de sortie
        $exit = ExitForm::factory()->create([
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
        
        // Ajouter un item au bon de sortie
        ExitItem::factory()->create([
            'exit_form_id' => $exit->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);
        
        // Valider le bon de sortie
        $validatedExit = $this->exitService->validate($exit->fresh());
        
        // Vérifier que le statut a été mis à jour
        $this->assertEquals('completed', $validatedExit->status);
        
        // Vérifier que la quantité en stock a été mise à jour
        $product->refresh();
        $this->assertEquals(7, $product->quantity);
        
        // Vérifier qu'un mouvement de stock a été créé
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 3,
            'user_id' => $user->id
        ]);
    }
    
    /** @test */
    public function it_prevents_exit_if_stock_is_insufficient()
    {
        // Créer un utilisateur
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        // Créer un produit avec un stock limité
        $product = Product::factory()->create([
            'quantity' => 5,
            'min_stock' => 2
        ]);
        
        // Créer un bon de sortie
        $exit = ExitForm::factory()->create([
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
        
        // Ajouter un item au bon de sortie avec une quantité trop élevée
        ExitItem::factory()->create([
            'exit_form_id' => $exit->id,
            'product_id' => $product->id,
            'quantity' => 10
        ]);
        
        // Tenter de valider le bon de sortie
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock insuffisant');
        
        $this->exitService->validate($exit->fresh());
        
        // Vérifier que le stock n'a pas été modifié
        $product->refresh();
        $this->assertEquals(5, $product->quantity);
    }
}
