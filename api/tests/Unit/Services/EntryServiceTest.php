<?php

namespace Tests\Unit\Services;

use App\Models\EntryForm;
use App\Models\EntryItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\EntryService;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class EntryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $stockService;
    protected $entryService;
    protected $user;
    protected $supplier;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();

        
        $this->stockService = Mockery::mock(StockService::class);
        
        
        $this->entryService = new EntryService($this->stockService);
        
        
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create(['quantity' => 10]);
        
        
        $this->actingAs($this->user);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    
    public function testDetectDuplicates()
    {
        
        $entry1 = EntryForm::factory()->create([
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now()->subDay(),
            'reference' => 'ENT-001',
            'status' => 'completed'
        ]);
        
        $entry2 = EntryForm::factory()->create([
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now(),
            'reference' => 'ENT-002',
            'status' => 'draft'
        ]);
        
        
        EntryItem::factory()->create([
            'entry_form_id' => $entry1->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
        
        EntryItem::factory()->create([
            'entry_form_id' => $entry2->id,
            'product_id' => $this->product->id,
            'quantity' => 3
        ]);
        
        
        $duplicates = $this->entryService->detectDuplicates([
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now()->format('Y-m-d')
        ]);
        
        
        $this->assertEquals(1, $duplicates->count());
        $this->assertEquals($entry1->id, $duplicates->first()->id);
    }

    
    public function testCancelDraftEntryForm()
    {
        
        $entryForm = EntryForm::factory()->create([
            'status' => 'draft',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->user->id
        ]);
        
        
        $cancelledEntry = $this->entryService->cancel($entryForm, 'Test d\'annulation');
        
        
        $this->assertEquals('cancelled', $cancelledEntry->status);
        
        
        $this->assertDatabaseHas('entry_form_histories', [
            'entry_form_id' => $entryForm->id,
            'field_name' => 'status',
            'old_value' => 'draft',
            'new_value' => 'cancelled',
            'change_reason' => 'Test d\'annulation'
        ]);
    }

    
    public function testCancelCompletedEntryForm()
    {
        
        $entryForm = EntryForm::factory()->create([
            'status' => 'completed',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->user->id
        ]);
        
        
        $item = EntryItem::factory()->create([
            'entry_form_id' => $entryForm->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
        
        
        $this->stockService->shouldReceive('updateStock')
            ->once()
            ->with(Mockery::type(Product::class), -5)
            ->andReturn($this->product);
            
        $this->stockService->shouldReceive('createStockMovement')
            ->once()
            ->andReturn(new \App\Models\StockMovement());
        
        
        $cancelledEntry = $this->entryService->cancel($entryForm);
        
        
        $this->assertEquals('cancelled', $cancelledEntry->status);
    }

    
    public function testGetEntriesByPeriod()
    {
        
        $entry1 = EntryForm::factory()->create([
            'status' => 'completed',
            'date' => Carbon::now()->subDays(5),
            'total' => 100
        ]);
        
        $entry2 = EntryForm::factory()->create([
            'status' => 'completed',
            'date' => Carbon::now()->subDays(2),
            'total' => 200
        ]);
        
        $entry3 = EntryForm::factory()->create([
            'status' => 'draft',
            'date' => Carbon::now()->subDays(3),
            'total' => 300
        ]);
        
        
        $report = $this->entryService->getEntriesByPeriod(
            Carbon::now()->subDays(7)->format('Y-m-d'),
            Carbon::now()->format('Y-m-d')
        );
        
        
        $this->assertEquals(300, $report['summary']['total_amount']);  
        $this->assertEquals(2, $report['summary']['entries_count']);
    }

    
    public function testIsValidDate()
    {
        
        $this->assertTrue($this->entryService->isValidDate(Carbon::now()->subDays(1)->format('Y-m-d')));
        
        
        $this->assertTrue($this->entryService->isValidDate(Carbon::now()->format('Y-m-d')));
        
        
        $this->assertFalse($this->entryService->isValidDate(Carbon::now()->addDays(1)->format('Y-m-d')));
    }
}
