<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\ProductResource;
use App\Http\Resources\StockMovementResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    /**
     * Période de mise en cache par défaut en minutes
     */
    const CACHE_DURATION = 60;

    /**
     * Retourne le rapport d'inventaire actuel avec les détails des produits, 
     * leur statut et regroupés par catégorie
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function inventory(Request $request)
    {
        // Validation des paramètres de filtre
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'nullable|in:low,normal,out',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Paramètres de filtrage et pagination
        $categoryId = $request->input('category_id');
        $status = $request->input('status');
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        
        // Clé de cache unique basée sur les paramètres de la requête
        $cacheKey = 'inventory_report_' . md5(serialize($request->all()));
        
        // Récupérer depuis le cache ou exécuter la requête
        $inventoryReport = Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($categoryId, $status, $search, $perPage) {
            // Base de la requête
            $query = Product::with('category')
                ->select('products.*', 
                    DB::raw('(products.quantity * products.price) as total_value'),
                    DB::raw('CASE 
                        WHEN products.quantity = 0 THEN "out"
                        WHEN products.quantity <= products.min_stock THEN "low" 
                        ELSE "normal" 
                        END as stock_status'
                    )
                );

            // Appliquer les filtres
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }
            
            if ($status) {
                if ($status == 'out') {
                    $query->where('quantity', 0);
                } elseif ($status == 'low') {
                    $query->whereColumn('quantity', '<=', 'min_stock')->where('quantity', '>', 0);
                } elseif ($status == 'normal') {
                    $query->whereColumn('quantity', '>', 'min_stock');
                }
            }
            
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('reference', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }
            
            // Récupérer les produits avec pagination
            $products = $query->paginate($perPage);

            // Préparer les données pour le rapport
            $data = [
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'total' => $products->total(),
                    'per_page' => $products->perPage(),
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                ],
                'summary' => [
                    'total_products' => Product::count(),
                    'out_of_stock' => Product::where('quantity', 0)->count(),
                    'low_stock' => Product::whereColumn('quantity', '<=', 'min_stock')->where('quantity', '>', 0)->count(),
                    'total_value' => Product::selectRaw('SUM(quantity * price) as total')->first()->total,
                ]
            ];
            
            // Ajouter les totaux par catégorie si aucun filtre de catégorie n'est appliqué
            if (!$categoryId) {                $categorySummary = Category::withCount('products')
                    ->with(['products' => function ($query) {
                        $query->select('category_id', 
                            DB::raw('SUM(quantity * price) as value')
                        )
                        ->groupBy('category_id');
                    }])
                    ->get()
                    ->map(function ($category) {
                        $totalValue = $category->products->isNotEmpty() ? $category->products->first()->value : 0;
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'product_count' => $category->products_count,
                            'total_value' => $totalValue,
                        ];
                    });
                    
                $data['categories'] = $categorySummary;
            }

            return $data;
        });

        return ApiResponse::success($inventoryReport, 'Rapport d\'inventaire généré avec succès');
    }

    /**
     * Retourne le rapport des mouvements de stock avec filtres et pagination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function movements(Request $request)
    {
        // Validation des paramètres
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'product_id' => 'nullable|exists:products,id',
            'category_id' => 'nullable|exists:categories,id',
            'type' => 'nullable|in:entry,exit',
            'user_id' => 'nullable|exists:users,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Paramètres de filtrage et pagination
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $productId = $request->input('product_id');
        $categoryId = $request->input('category_id');
        $type = $request->input('type');
        $userId = $request->input('user_id');
        $perPage = $request->input('per_page', 15);
        
        // Clé de cache unique
        $cacheKey = 'movements_report_' . md5(serialize($request->all()));
        
        // Récupérer depuis le cache ou exécuter la requête
        $movementsReport = Cache::remember($cacheKey, self::CACHE_DURATION, function () 
            use ($startDate, $endDate, $productId, $categoryId, $type, $userId, $perPage) {
            
            // Base de la requête avec les relations nécessaires
            $query = StockMovement::with(['product', 'product.category', 'user']);
            
            // Appliquer les filtres
            if ($startDate) {
                $query->whereDate('date', '>=', $startDate);
            }
            
            if ($endDate) {
                $query->whereDate('date', '<=', $endDate);
            }
            
            if ($productId) {
                $query->where('product_id', $productId);
            }
            
            if ($categoryId) {
                $query->whereHas('product', function ($q) use ($categoryId) {
                    $q->where('category_id', $categoryId);
                });
            }
            
            if ($type) {
                $query->where('type', $type);
            }
            
            if ($userId) {
                $query->where('user_id', $userId);
            }
            
            // Trier par date décroissante
            $query->orderBy('date', 'desc');
            
            // Récupérer les mouvements avec pagination
            $movements = $query->paginate($perPage);
            
            // Calculer les tendances et variations
            $trends = [];
            
            // Si une période est spécifiée, calculer les tendances par semaine ou par mois
            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                $diffInDays = $end->diffInDays($start);
                
                // Si la période est supérieure à 60 jours, grouper par mois, sinon par semaine
                if ($diffInDays > 60) {
                    $trends = StockMovement::whereBetween('date', [$start, $end])
                        ->select(
                            DB::raw('DATE_FORMAT(date, "%Y-%m") as period'),
                            DB::raw('SUM(CASE WHEN type = "entry" THEN quantity ELSE 0 END) as entries'),
                            DB::raw('SUM(CASE WHEN type = "exit" THEN quantity ELSE 0 END) as exits')
                        )
                        ->groupBy('period')
                        ->orderBy('period')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'period' => Carbon::createFromFormat('Y-m', $item->period)->format('Y-m'),
                                'entries' => $item->entries,
                                'exits' => $item->exits,
                                'net' => $item->entries - $item->exits,
                            ];
                        });
                } else {
                    $trends = StockMovement::whereBetween('date', [$start, $end])
                        ->select(
                            DB::raw('YEARWEEK(date) as yearweek'),
                            DB::raw('MIN(date) as week_start'),
                            DB::raw('SUM(CASE WHEN type = "entry" THEN quantity ELSE 0 END) as entries'),
                            DB::raw('SUM(CASE WHEN type = "exit" THEN quantity ELSE 0 END) as exits')
                        )
                        ->groupBy('yearweek')
                        ->orderBy('yearweek')
                        ->get()
                        ->map(function ($item) {
                            return [
                                'period' => Carbon::parse($item->week_start)->format('Y-m-d'),
                                'entries' => $item->entries,
                                'exits' => $item->exits,
                                'net' => $item->entries - $item->exits,
                            ];
                        });
                }
            }
            
            return [
                'movements' => StockMovementResource::collection($movements),
                'pagination' => [
                    'total' => $movements->total(),
                    'per_page' => $movements->perPage(),
                    'current_page' => $movements->currentPage(),
                    'last_page' => $movements->lastPage(),
                ],
                'summary' => [
                    'total_movements' => $movements->total(),
                    'entries_count' => ($type != 'exit') ? StockMovement::where('type', 'entry')
                        ->when($startDate, function ($q) use ($startDate) {
                            return $q->whereDate('date', '>=', $startDate);
                        })
                        ->when($endDate, function ($q) use ($endDate) {
                            return $q->whereDate('date', '<=', $endDate);
                        })
                        ->when($productId, function ($q) use ($productId) {
                            return $q->where('product_id', $productId);
                        })
                        ->when($categoryId, function ($q) use ($categoryId) {
                            return $q->whereHas('product', function ($q) use ($categoryId) {
                                $q->where('category_id', $categoryId);
                            });
                        })
                        ->count() : 0,
                    'exits_count' => ($type != 'entry') ? StockMovement::where('type', 'exit')
                        ->when($startDate, function ($q) use ($startDate) {
                            return $q->whereDate('date', '>=', $startDate);
                        })
                        ->when($endDate, function ($q) use ($endDate) {
                            return $q->whereDate('date', '<=', $endDate);
                        })
                        ->when($productId, function ($q) use ($productId) {
                            return $q->where('product_id', $productId);
                        })
                        ->when($categoryId, function ($q) use ($categoryId) {
                            return $q->whereHas('product', function ($q) use ($categoryId) {
                                $q->where('category_id', $categoryId);
                            });
                        })
                        ->count() : 0,
                ],
                'trends' => $trends,
            ];
        });
        
        return ApiResponse::success($movementsReport, 'Rapport des mouvements de stock généré avec succès');
    }

    /**
     * Retourne le rapport de valorisation du stock
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function valuation(Request $request)
    {
        // Validation des paramètres
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'date' => 'nullable|date',
            'compare_date' => 'nullable|date',
        ]);

        // Paramètres de filtrage
        $categoryId = $request->input('category_id');
        $date = $request->input('date');
        $compareDate = $request->input('compare_date');
        
        // Clé de cache unique
        $cacheKey = 'valuation_report_' . md5(serialize($request->all()));
        
        // Récupérer depuis le cache ou exécuter la requête
        $valuationReport = Cache::remember($cacheKey, self::CACHE_DURATION, function () 
            use ($categoryId, $date, $compareDate) {
            
            // Date courante pour la valorisation si non spécifiée
            $currentDate = $date ? Carbon::parse($date) : Carbon::now();
            
            // Requête pour la valorisation actuelle
            $query = Product::with('category')
                ->select(
                    'products.*', 
                    'categories.name as category_name',
                    DB::raw('(products.quantity * products.price) as total_value')
                )
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->when($categoryId, function ($q) use ($categoryId) {
                    return $q->where('products.category_id', $categoryId);
                });
            
            $products = $query->get();
            
            // Calcul des totaux
            $totalValue = $products->sum('total_value');
            
            // Regroupement par catégorie
            $byCategory = $products->groupBy('category_name')
                ->map(function ($group) {
                    return [
                        'category' => $group->first()->category_name,
                        'product_count' => $group->count(),
                        'total_value' => $group->sum('total_value'),
                    ];
                })
                ->values();
            
            // Préparation de la réponse
            $response = [
                'current_date' => $currentDate->format('Y-m-d'),
                'total_value' => $totalValue,
                'product_count' => $products->count(),
                'by_category' => $byCategory,
                'products' => ProductResource::collection($products),
            ];
            
            // Si une date de comparaison est fournie, ajouter l'historique de la valeur
            if ($compareDate) {
                $historicDate = Carbon::parse($compareDate);
                
                // Récupérer les mouvements de stock entre les deux dates
                $movements = StockMovement::whereBetween('date', [$historicDate, $currentDate])
                    ->with('product')
                    ->get();
                
                // Calculer la variation de valeur
                $valueChange = 0;
                
                foreach ($movements as $movement) {
                    if ($movement->type === 'entry') {
                        $valueChange += $movement->quantity * $movement->product->price;
                    } else {
                        $valueChange -= $movement->quantity * $movement->product->price;
                    }
                }
                
                // Ajouter à la réponse
                $response['comparison'] = [
                    'compare_date' => $historicDate->format('Y-m-d'),
                    'value_change' => $valueChange,
                    'percentage_change' => $totalValue != 0 ? round(($valueChange / $totalValue) * 100, 2) : 0,
                ];
            }
            
            return $response;
        });
        
        return ApiResponse::success($valuationReport, 'Rapport de valorisation du stock généré avec succès');
    }

    /**
     * Retourne le rapport de rotation des produits
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function turnover(Request $request)
    {
        // Validation des paramètres
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'category_id' => 'nullable|exists:categories,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        // Paramètres de filtrage et pagination
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $categoryId = $request->input('category_id');
        $perPage = $request->input('per_page', 15);
        
        // Clé de cache unique
        $cacheKey = 'turnover_report_' . md5(serialize($request->all()));
        
        // Récupérer depuis le cache ou exécuter la requête
        $turnoverReport = Cache::remember($cacheKey, self::CACHE_DURATION, function () 
            use ($startDate, $endDate, $categoryId, $perPage) {
            
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $diffInDays = $end->diffInDays($start);
            
            // Calcul de la rotation du stock
            // Formule: Sorties de stock pendant la période / ((Stock initial + Stock final) / 2)
            
            // Récupérer les mouvements pour la période
            $movements = StockMovement::with(['product'])
                ->whereBetween('date', [$start, $end])
                ->when($categoryId, function ($q) use ($categoryId) {
                    return $q->whereHas('product', function ($q) use ($categoryId) {
                        $q->where('category_id', $categoryId);
                    });
                })
                ->get();
            
            // Préparer le calcul par produit
            $productsData = [];
            
            // Calculer les quantités d'entrée et de sortie par produit
            foreach ($movements as $movement) {
                $productId = $movement->product_id;
                
                if (!isset($productsData[$productId])) {
                    $productsData[$productId] = [
                        'product' => $movement->product,
                        'entries' => 0,
                        'exits' => 0,
                    ];
                }
                
                if ($movement->type === 'entry') {
                    $productsData[$productId]['entries'] += $movement->quantity;
                } else {
                    $productsData[$productId]['exits'] += $movement->quantity;
                }
            }
            
            // Calculer le taux de rotation et autres métriques
            $products = collect($productsData)->map(function ($data) use ($diffInDays) {
                $product = $data['product'];
                $entries = $data['entries'];
                $exits = $data['exits'];
                
                // Stock initial estimé = Stock actuel + Sorties - Entrées
                $initialStock = $product->quantity + $exits - $entries;
                // Stock moyen = (Stock initial + Stock final) / 2
                $averageStock = ($initialStock + $product->quantity) / 2;
                
                // Taux de rotation = Sorties / Stock moyen
                $turnoverRate = $averageStock > 0 ? $exits / $averageStock : 0;
                
                // Convertir en annuel si la période est différente d'un an
                if ($diffInDays != 365) {
                    $turnoverRate = $turnoverRate * (365 / $diffInDays);
                }
                
                // Jours de couverture = 365 / Taux de rotation
                $coverageDays = $turnoverRate > 0 ? round(365 / $turnoverRate) : 0;
                
                return [
                    'id' => $product->id,
                    'reference' => $product->reference,
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'current_quantity' => $product->quantity,
                    'entries' => $entries,
                    'exits' => $exits,
                    'turnover_rate' => round($turnoverRate, 2),
                    'coverage_days' => $coverageDays,
                    'turnover_classification' => $this->classifyTurnoverRate($turnoverRate),
                ];
            })->values();
            
            // Trier par taux de rotation (décroissant)
            $sortedProducts = $products->sortByDesc('turnover_rate')->values();
            
            // Paginer manuellement
            $page = request()->input('page', 1);
            $paginatedProducts = $sortedProducts->forPage($page, $perPage);
            
            // Calculer des métriques globales
            $averageTurnoverRate = $products->avg('turnover_rate');
            
            // Catégoriser les produits
            $highTurnover = $products->where('turnover_rate', '>=', 6)->count();
            $mediumTurnover = $products->where('turnover_rate', '<', 6)->where('turnover_rate', '>=', 2)->count();
            $lowTurnover = $products->where('turnover_rate', '<', 2)->count();
            
            return [
                'products' => $paginatedProducts,
                'pagination' => [
                    'total' => $products->count(),
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => ceil($products->count() / $perPage),
                ],
                'summary' => [
                    'period_days' => $diffInDays,
                    'average_turnover_rate' => round($averageTurnoverRate, 2),
                    'high_turnover_count' => $highTurnover,
                    'medium_turnover_count' => $mediumTurnover,
                    'low_turnover_count' => $lowTurnover,
                ],
                'range' => [
                    'start_date' => $start->format('Y-m-d'),
                    'end_date' => $end->format('Y-m-d'),
                ],
            ];
        });
        
        return ApiResponse::success($turnoverReport, 'Rapport de rotation des produits généré avec succès');
    }
    
    /**
     * Classifie le taux de rotation en catégories
     *
     * @param float $rate
     * @return string
     */
    private function classifyTurnoverRate($rate)
    {
        if ($rate >= 6) {
            return 'high'; // Rotation élevée
        } elseif ($rate >= 2) {
            return 'medium'; // Rotation moyenne
        } else {
            return 'low'; // Rotation faible
        }
    }
}
