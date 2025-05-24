<?php

namespace Tests\Feature;

use App\Models\ExitForm;
use App\Models\ExitItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class ExitFormFeatureTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $magasinier;
    protected $product1;
    protected $product2;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer des utilisateurs de test
        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin.test@gestistock.com',
            'password' => bcrypt('password'),
            'role' => 'admin'
        ]);

        $this->magasinier = User::factory()->create([
            'name' => 'Magasinier Test',
            'email' => 'magasinier.test@gestistock.com',
            'password' => bcrypt('password'),
            'role' => 'magasinier'
        ]);

        // Créer des produits de test
        $this->product1 = Product::factory()->create([
            'name' => 'Produit Test 1',
            'reference' => 'TEST001',
            'description' => 'Produit de test 1',
            'quantity' => 100,
            'unit_price' => 15.50
        ]);

        $this->product2 = Product::factory()->create([
            'name' => 'Produit Test 2',
            'reference' => 'TEST002',
            'description' => 'Produit de test 2',
            'quantity' => 200,
            'unit_price' => 25.75
        ]);

        // Obtenir un token JWT pour l'admin
        $this->token = JWTAuth::fromUser($this->admin);
    }

    /** @test */
    public function it_can_list_exit_forms()
    {
        // Créer quelques bons de sortie
        ExitForm::factory()->count(3)->create(['user_id' => $this->admin->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/exit-forms');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'success',
                'message',
            ]);
    }

    /** @test */
    public function it_can_create_an_exit_form()
    {
        $exitFormData = [
            'reference' => 'BS-TEST-' . uniqid(),
            'date' => now()->format('Y-m-d'),
            'destination' => 'Service Test',
            'reason' => 'Test de création',
            'notes' => 'Notes de test',
            'status' => 'draft',
            'user_id' => $this->admin->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 5
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 3
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/exit-forms', $exitFormData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reference',
                    'date',
                    'destination',
                    'reason',
                    'notes',
                    'status',
                    'user',
                    'items'
                ],
                'success',
                'message',
            ]);

        $this->assertDatabaseHas('exit_forms', [
            'reference' => $exitFormData['reference'],
            'destination' => $exitFormData['destination'],
            'status' => 'draft',
        ]);

        // Vérifier que les items ont bien été créés
        $this->assertDatabaseHas('exit_items', [
            'product_id' => $this->product1->id,
            'quantity' => 5
        ]);

        $this->assertDatabaseHas('exit_items', [
            'product_id' => $this->product2->id,
            'quantity' => 3
        ]);
    }

    /** @test */
    public function it_can_show_an_exit_form()
    {
        $exitForm = ExitForm::factory()
            ->create(['user_id' => $this->admin->id]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product1->id,
            'quantity' => 5
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/exit-forms/' . $exitForm->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'reference',
                    'date',
                    'destination',
                    'status',
                    'user',
                    'items'
                ],
                'success',
                'message',
            ]);
    }

    /** @test */
    public function it_can_update_an_exit_form()
    {
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'draft'
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product1->id,
            'quantity' => 5
        ]);

        $updatedData = [
            'reference' => $exitForm->reference, // Conserver la même référence
            'date' => now()->format('Y-m-d'),
            'destination' => 'Service Mis à jour',
            'reason' => 'Test de mise à jour',
            'notes' => 'Notes mises à jour',
            'status' => 'draft',
            'user_id' => $this->admin->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 7 // Quantité mise à jour
                ],
                [
                    'product_id' => $this->product2->id, // Nouvel item
                    'quantity' => 3
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/exit-forms/' . $exitForm->id, $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'success',
                'message',
            ]);

        $this->assertDatabaseHas('exit_forms', [
            'id' => $exitForm->id,
            'destination' => 'Service Mis à jour',
            'reason' => 'Test de mise à jour',
        ]);

        // Vérifier que les nouveaux items sont présents
        $this->assertDatabaseHas('exit_items', [
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product2->id,
            'quantity' => 3
        ]);
    }

    /** @test */
    public function it_can_detect_duplicates()
    {
        $reference = 'BS-DUPL-' . uniqid();
        
        // Créer un bon de sortie
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'reference' => $reference,
                'status' => 'draft'
            ]);

        // Données pour la vérification de doublons
        $duplicateData = [
            'reference' => $reference
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/exit-forms/check-duplicates', $duplicateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'has_duplicates' => true,
                ]
            ]);
    }

    /** @test */
    public function it_can_validate_an_exit_form()
    {
        // S'assurer que les produits ont suffisamment de stock
        $this->product1->update(['quantity' => 100]);
        $this->product2->update(['quantity' => 100]);

        // Créer un bon de sortie à valider
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'draft'
            ]);

        // Ajouter des items
        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product1->id,
            'quantity' => 5
        ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product2->id,
            'quantity' => 3
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/exit-forms/' . $exitForm->id . '/validate', [
            'validation_note' => 'Validation de test'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'completed'
                ]
            ]);

        // Vérifier que le stock a été mis à jour
        $this->product1->refresh();
        $this->product2->refresh();
        
        $this->assertEquals(95, $this->product1->quantity); // 100 - 5
        $this->assertEquals(97, $this->product2->quantity); // 100 - 3
        
        // Vérifier les mouvements de stock
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product1->id,
            'type' => 'exit',
            'quantity' => 5
        ]);
        
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product2->id,
            'type' => 'exit',
            'quantity' => 3
        ]);
    }

    /** @test */
    public function it_can_cancel_an_exit_form()
    {
        // Créer un bon de sortie complété
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed'
            ]);

        // Ajouter des items
        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product1->id,
            'quantity' => 5
        ]);

        // Simuler la réduction de stock qui aurait eu lieu lors de la validation
        $this->product1->decrement('quantity', 5);
        $initialQuantity = $this->product1->quantity;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/exit-forms/' . $exitForm->id . '/cancel', [
            'reason' => 'Annulation de test'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => 'cancelled'
                ]
            ]);

        // Vérifier que le stock a été réajusté
        $this->product1->refresh();
        $this->assertEquals($initialQuantity + 5, $this->product1->quantity);
        
        // Vérifier l'historique
        $this->assertDatabaseHas('exit_form_histories', [
            'exit_form_id' => $exitForm->id,
            'field_name' => 'status',
            'old_value' => 'completed',
            'new_value' => 'cancelled',
            'change_reason' => 'Annulation de test'
        ]);
    }

    /** @test */
    public function it_can_get_exit_form_history()
    {
        // Créer un bon de sortie
        $exitForm = ExitForm::factory()->create(['user_id' => $this->admin->id]);
        
        // Ajouter quelques entrées d'historique
        \App\Models\ExitFormHistory::factory()->count(3)->create([
            'exit_form_id' => $exitForm->id,
            'user_id' => $this->admin->id
        ]);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/exit-forms/' . $exitForm->id . '/history');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'exit_form',
                    'history'
                ],
                'success',
                'message'
            ]);
        
        // Vérifier qu'il y a bien 3 entrées d'historique
        $this->assertCount(3, $response->json('data.history'));
    }

    /** @test */
    public function it_cant_validate_exit_form_without_items()
    {
        // Créer un bon de sortie sans items
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'draft'
            ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/exit-forms/' . $exitForm->id . '/validate');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
        
        // Vérifier que le message d'erreur est correct
        $this->assertStringContainsString('Impossible de valider un bon de sortie sans articles', $response->json('message'));
    }

    /** @test */
    public function it_cant_validate_exit_form_with_insufficient_stock()
    {
        // S'assurer que le produit a peu de stock
        $this->product1->update(['quantity' => 3]);

        // Créer un bon de sortie avec une quantité excessive
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'draft'
            ]);

        // Ajouter un item avec une quantité supérieure au stock
        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product1->id,
            'quantity' => 10 // Plus que les 3 en stock
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/exit-forms/' . $exitForm->id . '/validate');

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
            
        // Vérifier que le message d'erreur mentionne le stock insuffisant
        $this->assertStringContainsString('Stock insuffisant', $response->json('message'));
    }

    /** @test */
    public function it_cant_update_completed_exit_form()
    {
        // Créer un bon de sortie avec statut 'completed'
        $exitForm = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed'
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm->id,
            'product_id' => $this->product1->id,
            'quantity' => 5
        ]);

        $updatedData = [
            'reference' => $exitForm->reference,
            'date' => now()->format('Y-m-d'),
            'destination' => 'Service modifié',
            'user_id' => $this->admin->id,
            'status' => 'completed',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 7
                ]
            ]
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->putJson('/api/exit-forms/' . $exitForm->id, $updatedData);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false
            ]);
    }
    
    /** @test */
    public function it_can_generate_exit_forms_by_period_report()
    {
        // Créer plusieurs bons de sortie à des dates différentes
        $exitForm1 = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed',
                'date' => now()->subDays(5)
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm1->id,
            'product_id' => $this->product1->id,
            'quantity' => 10
        ]);

        $exitForm2 = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed',
                'date' => now()->subDays(3)
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm2->id,
            'product_id' => $this->product2->id,
            'quantity' => 5
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/reports/exits/by-period?start_date=' . 
            now()->subDays(10)->format('Y-m-d') . '&end_date=' . 
            now()->format('Y-m-d'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'period' => [
                        'start',
                        'end'
                    ],
                    'summary' => [
                        'exits_count',
                        'items_count',
                        'total_quantity'
                    ],
                    'exits'
                ],
                'success',
                'message'
            ]);

        // Vérifier que le rapport contient bien les 2 bons
        $this->assertEquals(2, $response->json('data.summary.exits_count'));
    }
    
    /** @test */
    public function it_can_generate_exit_forms_by_destination_report()
    {
        // Créer plusieurs bons de sortie vers des destinations différentes
        $exitForm1 = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed',
                'destination' => 'Service A'
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm1->id,
            'product_id' => $this->product1->id,
            'quantity' => 7
        ]);

        $exitForm2 = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed',
                'destination' => 'Service A' // Même destination
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm2->id,
            'product_id' => $this->product2->id,
            'quantity' => 3
        ]);

        $exitForm3 = ExitForm::factory()
            ->create([
                'user_id' => $this->admin->id,
                'status' => 'completed',
                'destination' => 'Service B' // Destination différente
            ]);

        ExitItem::factory()->create([
            'exit_form_id' => $exitForm3->id,
            'product_id' => $this->product2->id,
            'quantity' => 5
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/reports/exits/by-destination');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'period',
                    'destinations' => [
                        '*' => [
                            'destination',
                            'exits_count',
                            'items_count',
                            'total_quantity'
                        ]
                    ]
                ],
                'success',
                'message'
            ]);

        // Vérifier qu'il y a bien 2 destinations différentes
        $this->assertCount(2, $response->json('data.destinations'));
    }
}
