<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EntryForm;
use App\Models\ExitForm;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $token;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);

        
        $this->token = auth()->login($this->user);

        
        $this->createTestData();
    }

    
    private function createTestData()
    {
        
        $categories = Category::factory()->count(3)->create();
        
        
        $supplier = Supplier::factory()->create();

        
        $products = [];
        foreach ($categories as $category) {
            $productsForCategory = Product::factory()->count(5)->create([
                'category_id' => $category->id,
                'price' => rand(10, 100),
                'quantity' => rand(10, 50),
                'min_stock' => 5,
            ]);
            
            $products = array_merge($products, $productsForCategory->toArray());
        }

        
        Product::factory()->create([
            'category_id' => $categories[0]->id,
            'quantity' => 0,
            'min_stock' => 5,
        ]);

        
        Product::factory()->create([
            'category_id' => $categories[1]->id,
            'quantity' => 3,
            'min_stock' => 10,
        ]);

        
        $entryForm = EntryForm::factory()->create([
            'supplier_id' => $supplier->id,
            'date' => Carbon::now()->subDays(5),
            'status' => 'validated',
            'total' => 500,
            'user_id' => $this->user->id,
        ]);

        
        $exitForm = ExitForm::factory()->create([
            'date' => Carbon::now()->subDays(3),
            'status' => 'validated',
            'user_id' => $this->user->id,
        ]);

        
        foreach (range(1, 10) as $i) {
            StockMovement::factory()->create([
                'product_id' => $products[array_rand($products)]['id'],
                'type' => $i % 2 == 0 ? 'in' : 'out',
                'quantity' => rand(1, 10),
                'date' => Carbon::now()->subDays(rand(1, 20)),
                'user_id' => $this->user->id,
            ]);
        }
    }

    
    public function it_returns_dashboard_summary()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'overview' => [
                        'total_products',
                        'total_categories',
                        'total_stock_value',
                        'out_of_stock_count',
                        'low_stock_count',
                        'stock_health_percentage'
                    ],
                    'activity' => [
                        'period',
                        'entries_count',
                        'exits_count',
                        'entries_value',
                        'exits_value',
                        'balance'
                    ],
                    'top_products'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Aperçu du tableau de bord',
            ]);
    }

    
    public function it_returns_stock_trends()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/stock-trends');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'period_type',
                    'start_date',
                    'end_date',
                    'trends'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Tendances des niveaux de stock',
            ]);
    }

    
    public function it_returns_category_analysis()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/category-analysis');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'categories' => [
                        '*' => [
                            'id',
                            'name',
                            'product_count',
                            'total_quantity',
                            'total_value',
                            'movements_last_30_days',
                            'rotation_rate'
                        ]
                    ],
                    'period'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Analyse par catégorie',
            ]);
    }

    
    public function it_returns_product_performance()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/product-performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'period',
                    'sort_by',
                    'products' => [
                        '*' => [
                            'id',
                            'name',
                            'reference',
                            'category',
                            'current_stock',
                            'performance'
                        ]
                    ]
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Analyse des performances des produits',
            ]);
    }

    
    public function it_returns_activity_metrics()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/activity-metrics');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'period',
                    'summary',
                    'activity_by_period'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Métriques d\'activité',
            ]);
    }

    
    public function it_returns_alerts()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/alerts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'out_of_stock',
                    'low_stock',
                    'predicted_stockouts',
                    'potential_overstocks'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Alertes du tableau de bord',
            ]);
    }

    
    public function unauthorized_users_cannot_access_dashboard()
    {
        
        $response = $this->getJson('/api/dashboard/summary');
        $response->assertStatus(401);

        
        $regularUser = User::factory()->create([
            'role' => 'user' 
        ]);
        
        $regularToken = auth()->login($regularUser);
        
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $regularToken,
        ])->getJson('/api/dashboard/summary');
        
        $response->assertStatus(403);
    }

    
    public function it_filters_stock_trends_by_category()
    {
        $category = Category::first();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/dashboard/stock-trends?category_id=' . $category->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Tendances des niveaux de stock',
            ]);
    }

    
    public function it_sorts_product_performance_by_different_criteria()
    {
        $sortCriteria = ['rotation', 'value', 'turnover', 'frequency'];
        
        foreach ($sortCriteria as $criteria) {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->getJson('/api/dashboard/product-performance?sort_by=' . $criteria);

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Analyse des performances des produits',
                    'data' => [
                        'sort_by' => $criteria
                    ]
                ]);
        }
    }
}
