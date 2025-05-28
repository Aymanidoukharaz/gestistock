<?php

namespace Tests\Feature;

use App\Models\EntryForm;
use App\Models\EntryItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

class EntryFormFeatureTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $admin;
    protected $magasinier;
    protected $supplier;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->magasinier = User::factory()->create(['role' => 'magasinier']);
        
        
        $this->supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create(['quantity' => 10]);
    }

    
    public function testCreateEntryForm()
    {
        Passport::actingAs($this->admin);
        
        $response = $this->postJson('/api/entry-forms', [
            'reference' => 'ENT-' . rand(1000, 9999),
            'date' => now()->format('Y-m-d'),
            'supplier_id' => $this->supplier->id,
            'status' => 'draft',
            'user_id' => $this->admin->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'unit_price' => 10.5
                ]
            ]
        ]);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reference',
                    'date',
                    'supplier',
                    'items'
                ],
                'success',
                'message'
            ]);
    }

    
    public function testCheckDuplicates()
    {
        Passport::actingAs($this->admin);
        
        
        $entry = EntryForm::factory()->create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'completed'
        ]);
        
        
        $response = $this->postJson('/api/entry-forms/check-duplicates', [
            'supplier_id' => $this->supplier->id,
            'date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'has_duplicates' => true
                ],
                'success' => true
            ]);
    }

    
    public function testCancelEntryForm()
    {
        Passport::actingAs($this->admin);
        
        
        $entry = EntryForm::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => 'draft'
        ]);
        
        
        $response = $this->postJson("/api/entry-forms/{$entry->id}/cancel", [
            'cancel_reason' => 'Test d\'annulation'
        ]);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'status' => 'cancelled'
                ],
                'success' => true
            ]);
    }

    
    public function testEntryFormHistory()
    {
        Passport::actingAs($this->admin);
        
        
        $entry = EntryForm::factory()->create([
            'supplier_id' => $this->supplier->id,
            'status' => 'draft'
        ]);
        
        
        $this->postJson("/api/entry-forms/{$entry->id}/validate");
        
        
        $response = $this->getJson("/api/entry-forms/{$entry->id}/history");
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'entry_id',
                    'reference',
                    'history' => [
                        '*' => [
                            'id',
                            'field',
                            'old_value',
                            'new_value',
                            'date'
                        ]
                    ]
                ],
                'success',
                'message'
            ]);
    }

    
    public function testReportByPeriod()
    {
        Passport::actingAs($this->admin);
        
        
        EntryForm::factory()->count(3)->create([
            'status' => 'completed',
            'date' => now()->subDays(5)->format('Y-m-d')
        ]);
        
        
        $response = $this->getJson('/api/reports/entries/by-period', [
            'start_date' => now()->subDays(10)->format('Y-m-d'),
            'end_date' => now()->format('Y-m-d')
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'period',
                    'summary',
                    'entries'
                ],
                'success',
                'message'
            ]);
    }
}
