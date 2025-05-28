<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EntryForm;
use App\Models\ExitForm;
use App\Models\Product;
use App\Models\StockMovement;
use App\Http\Resources\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    
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
            
            return [
                'totalProducts' => $totalProducts,
                'lowStockAlerts' => $lowStockAlerts,
                'totalValue' => $totalValue,
                'movementsToday' => $movementsToday
            ];
        });
        
        return ApiResponse::success($data, 'Aperçu du tableau de bord récupéré avec succès');
    }
    
    
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
    
}
