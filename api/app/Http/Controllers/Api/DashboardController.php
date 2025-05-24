<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EntryForm;
use App\Models\ExitForm;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Récupère les KPIs principaux pour le tableau de bord
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function summary(Request $request)
    {
        // Utiliser le cache pour optimiser les performances (TTL: 10 minutes)
        $cacheKey = 'dashboard_summary_' . auth()->id();
        $cacheDuration = 10; // minutes

        $data = Cache::remember($cacheKey, $cacheDuration * 60, function () {
            // Nombre total de produits
            $totalProducts = Product::count();
            
            // Produits en alerte de stock
            $lowStockAlerts = Product::whereRaw('quantity < min_stock')->count();
            
            // Valeur totale du stock
            $totalValue = Product::sum(\DB::raw('price * quantity'));
            
            // Mouvements du jour
            $today = Carbon::today();
            $movementsToday = StockMovement::whereDate('created_at', $today)->count();
            
            // Calcul des entrées et sorties des 7 derniers jours
            $last7Days = Carbon::now()->subDays(7);
            
            $recentEntries = StockMovement::where('created_at', '>=', $last7Days)
                ->where('type', 'entry')
                ->get();
            
            $recentExits = StockMovement::where('created_at', '>=', $last7Days)
                ->where('type', 'exit')
                ->get();
            
            $entriesValue = 0;
            foreach ($recentEntries as $entry) {
                $product = Product::find($entry->product_id);
                if ($product) {
                    $entriesValue += $product->price * $entry->quantity;
                }
            }
            
            $exitsValue = 0;
            foreach ($recentExits as $exit) {
                $product = Product::find($exit->product_id);
                if ($product) {
                    $exitsValue += $product->price * $exit->quantity;
                }
            }
            
            return [
                'totalProducts' => $totalProducts,
                'lowStockAlerts' => $lowStockAlerts,
                'totalValue' => $totalValue,
                'movementsToday' => $movementsToday,
                'entriesValue' => $entriesValue,
                'exitsValue' => $exitsValue
            ];
        });
        
        return ApiResponse::success($data, 'Aperçu du tableau de bord récupéré avec succès');
    }
    
    /**
     * Récupère les mouvements récents pour le tableau de bord
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recentMovements()
    {
        $cacheKey = 'dashboard_recent_movements_' . auth()->id();
        $cacheDuration = 5; // minutes
        
        $data = Cache::remember($cacheKey, $cacheDuration * 60, function () {
            $recentMovements = StockMovement::with('product')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($movement) {
                    return [
                        'id' => $movement->id,
                        'productName' => $movement->product ? $movement->product->name : 'Produit inconnu',
                        'type' => $movement->type,
                        'quantity' => $movement->quantity,
                        'date' => $movement->created_at->toIso8601String()
                    ];
                });
                
            return $recentMovements;
        });
        
        return ApiResponse::success($data, 'Mouvements récents récupérés avec succès');
    }
    
    /**
     * Récupère la répartition par catégorie pour le tableau de bord
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categoryAnalysis()
    {
        $cacheKey = 'dashboard_category_analysis_' . auth()->id();
        $cacheDuration = 30; // minutes
        
        $data = Cache::remember($cacheKey, $cacheDuration * 60, function () {
            // Récupérer les catégories avec le nombre de produits
            $categories = Category::withCount('products')->get();
            
            $categoryData = $categories->map(function ($category, $index) {
                // Tableau de couleurs du frontend
                $categoryColors = [
                    "#8b5cf6", // violet-500
                    "#ec4899", // pink-500
                    "#3b82f6", // blue-500
                    "#10b981", // emerald-500
                    "#f59e0b", // amber-500
                    "#ef4444", // red-500
                    "#6366f1", // indigo-500
                    "#14b8a6", // teal-500
                    "#f97316", // orange-500
                    "#8b5cf6", // violet-500 (répété si besoin)
                ];
                
                return [
                    'name' => $category->name,
                    'value' => $category->products_count,
                    'fill' => $categoryColors[$index % count($categoryColors)]
                ];
            });
            
            return $categoryData;
        });
        
        return ApiResponse::success($data, 'Analyse par catégorie récupérée avec succès');
    }
    
    /**
     * Récupère les données pour le graphique de mouvements de stock
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function stockMovementChart()
    {
        $cacheKey = 'dashboard_stock_movement_chart_' . auth()->id();
        $cacheDuration = 10; // minutes
        
        $data = Cache::remember($cacheKey, $cacheDuration * 60, function () {
            // Récupérer les données des 7 derniers jours
            $result = [];
            
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dateStr = $date->toDateString();
                $formattedDate = $date->format('d/m');
                
                $entriesForDay = StockMovement::whereDate('created_at', $dateStr)
                    ->where('type', 'entry')
                    ->sum('quantity');
                    
                $exitsForDay = StockMovement::whereDate('created_at', $dateStr)
                    ->where('type', 'exit')
                    ->sum('quantity');
                
                $result[] = [
                    'date' => $formattedDate,
                    'entrées' => (int)$entriesForDay,
                    'sorties' => (int)$exitsForDay
                ];
            }
            
            return $result;
        });
        
        return ApiResponse::success($data, 'Données du graphique de mouvements récupérées avec succès');
    }
}
