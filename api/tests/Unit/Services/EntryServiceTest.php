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

        // Créer un mock du StockService
        $this->stockService = Mockery::mock(StockService::class);
        
        // Injecter le mock dans EntryService
        $this->entryService = new EntryService($this->stockService);
        
        // Créer des données de test
        $this->user = User::factory()->create(['role' => 'admin']);
        $this->supplier = Supplier::factory()->create();
        $this->product = Product::factory()->create(['quantity' => 10]);
        
        // Se connecter en tant qu'admin pour les tests
        $this->actingAs($this->user);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test de la détection des doublons.
     */
    public function testDetectDuplicates()
    {
        // Créer deux bons d'entrée similaires
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
        
        // Ajouter un item à chaque bon
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
        
        // Tester la détection de doublons
        $duplicates = $this->entryService->detectDuplicates([
            'supplier_id' => $this->supplier->id,
            'date' => Carbon::now()->format('Y-m-d')
        ]);
        
        // Vérifier qu'un doublon est détecté (entry1)
        $this->assertEquals(1, $duplicates->count());
        $this->assertEquals($entry1->id, $duplicates->first()->id);
    }

    /**
     * Test d'annulation d'un bon d'entrée en statut draft.
     */
    public function testCancelDraftEntryForm()
    {
        // Créer un bon d'entrée en statut draft
        $entryForm = EntryForm::factory()->create([
            'status' => 'draft',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->user->id
        ]);
        
        // Annuler le bon
        $cancelledEntry = $this->entryService->cancel($entryForm, 'Test d\'annulation');
        
        // Vérifier que le statut a été mis à jour
        $this->assertEquals('cancelled', $cancelledEntry->status);
        
        // Vérifier que l'historique a été créé
        $this->assertDatabaseHas('entry_form_histories', [
            'entry_form_id' => $entryForm->id,
            'field_name' => 'status',
            'old_value' => 'draft',
            'new_value' => 'cancelled',
            'change_reason' => 'Test d\'annulation'
        ]);
    }

    /**
     * Test d'annulation d'un bon d'entrée en statut completed.
     */
    public function testCancelCompletedEntryForm()
    {
        // Créer un bon d'entrée en statut completed
        $entryForm = EntryForm::factory()->create([
            'status' => 'completed',
            'supplier_id' => $this->supplier->id,
            'user_id' => $this->user->id
        ]);
        
        // Créer un item pour le bon d'entrée
        $item = EntryItem::factory()->create([
            'entry_form_id' => $entryForm->id,
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);
        
        // Configurer les attentes du mock
        $this->stockService->shouldReceive('updateStock')
            ->once()
            ->with(Mockery::type(Product::class), -5)
            ->andReturn($this->product);
            
        $this->stockService->shouldReceive('createStockMovement')
            ->once()
            ->andReturn(new \App\Models\StockMovement());
        
        // Annuler le bon
        $cancelledEntry = $this->entryService->cancel($entryForm);
        
        // Vérifier que le statut a été mis à jour
        $this->assertEquals('cancelled', $cancelledEntry->status);
    }

    /**
     * Test de génération de rapport par période.
     */
    public function testGetEntriesByPeriod()
    {
        // Créer plusieurs bons d'entrée à des dates différentes
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
        
        // Générer le rapport
        $report = $this->entryService->getEntriesByPeriod(
            Carbon::now()->subDays(7)->format('Y-m-d'),
            Carbon::now()->format('Y-m-d')
        );
        
        // Vérifier les résultats
        $this->assertEquals(300, $report['summary']['total_amount']);  // Seulement les deux complétés
        $this->assertEquals(2, $report['summary']['entries_count']);
    }

    /**
     * Test de validation des dates du bon d'entrée.
     */
    public function testIsValidDate()
    {
        // Date dans le passé (valide)
        $this->assertTrue($this->entryService->isValidDate(Carbon::now()->subDays(1)->format('Y-m-d')));
        
        // Date actuelle (valide)
        $this->assertTrue($this->entryService->isValidDate(Carbon::now()->format('Y-m-d')));
        
        // Date future (invalide)
        $this->assertFalse($this->entryService->isValidDate(Carbon::now()->addDays(1)->format('Y-m-d')));
    }
}
