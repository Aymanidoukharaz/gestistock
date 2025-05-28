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
    
    
    public function it_can_validate_an_entry_form_and_update_stock()
    {
        
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        
        $product = Product::factory()->create([
            'quantity' => 10,
            'price' => 100.00,
            'min_stock' => 5
        ]);
        
        
        $entry = EntryForm::factory()->create([
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
        
        
        EntryItem::factory()->create([
            'entry_form_id' => $entry->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'unit_price' => 120.00,
            'total' => 600.00
        ]);
        
        
        $validatedEntry = $this->entryService->validate($entry->fresh());
        
        
        $this->assertEquals('completed', $validatedEntry->status);
        
        
        $product->refresh();
        $this->assertEquals(15, $product->quantity);
        
        
        $this->assertEquals(120.00, $product->price);
        
        
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'entry',
            'quantity' => 5,
            'user_id' => $user->id
        ]);
    }
    
    
    public function it_can_validate_an_exit_form_and_update_stock()
    {
        
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        
        $product = Product::factory()->create([
            'quantity' => 10,
            'price' => 100.00,
            'min_stock' => 5
        ]);
        
        
        $exit = ExitForm::factory()->create([
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
        
        
        ExitItem::factory()->create([
            'exit_form_id' => $exit->id,
            'product_id' => $product->id,
            'quantity' => 3
        ]);
        
        
        $validatedExit = $this->exitService->validate($exit->fresh());
        
        
        $this->assertEquals('completed', $validatedExit->status);
        
        
        $product->refresh();
        $this->assertEquals(7, $product->quantity);
        
        
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'exit',
            'quantity' => 3,
            'user_id' => $user->id
        ]);
    }
    
    
    public function it_prevents_exit_if_stock_is_insufficient()
    {
        
        $user = User::factory()->create(['role' => 'admin']);
        Auth::login($user);
        
        
        $product = Product::factory()->create([
            'quantity' => 5,
            'min_stock' => 2
        ]);
        
        
        $exit = ExitForm::factory()->create([
            'status' => 'draft',
            'user_id' => $user->id,
        ]);
        
        
        ExitItem::factory()->create([
            'exit_form_id' => $exit->id,
            'product_id' => $product->id,
            'quantity' => 10
        ]);
        
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Stock insuffisant');
        
        $this->exitService->validate($exit->fresh());
        
        
        $product->refresh();
        $this->assertEquals(5, $product->quantity);
    }
}
