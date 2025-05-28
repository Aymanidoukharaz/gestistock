<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\EntryFormController;
use App\Http\Controllers\Api\ExitFormController;
use App\Http\Controllers\Api\DashboardController;



// Routes d'authentification
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
      // Routes protégées par le middleware auth:api
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('user', [AuthController::class, 'me']);
        Route::post('register', [AuthController::class, 'register']);
    });
});

// Routes protégées nécessitant une authentification
Route::middleware('auth:api')->group(function () {
    
    // Routes communes à tous les utilisateurs authentifiés (admin et magasinier)
    Route::group(['middleware' => 'role:admin,magasinier'], function () {
        // Consultation des produits
        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/categories', [ProductController::class, 'categories']); // New route for product categories
        Route::get('products/{product}', [ProductController::class, 'show']);
        
        // Consultation des catégories
        
        
        // Consultation des fournisseurs
       
        
        // Consultation des mouvements de stock
        Route::get('stock-movements', [StockMovementController::class, 'index']);
        Route::get('stock-movements/{stockMovement}', [StockMovementController::class, 'show']);
        
        Route::get('/stock-movements/product/{product}', [StockMovementController::class, 'getByProduct']);
        // Consultation des bons d'entrée
        Route::get('entry-forms', [EntryFormController::class, 'index']);
        Route::get('entry-forms/{entryForm}', [EntryFormController::class, 'show']);
        
        // Consultation des bons de sortie
        Route::get('exit-forms', [ExitFormController::class, 'index']);
        Route::get('exit-forms/{exitForm}', [ExitFormController::class, 'show']);
    });
    
    // Routes réservées aux administrateurs
    Route::group(['middleware' => 'role:admin'], function () {
        // Gestion des utilisateurs (admin uniquement)
        Route::get('users', [\App\Http\Controllers\Api\UserController::class, 'index']);
        Route::post('users', [\App\Http\Controllers\Api\AuthController::class, 'register']);
        Route::get('users/{id}', [\App\Http\Controllers\Api\UserController::class, 'show']);
        Route::put('users/{id}', [\App\Http\Controllers\Api\UserController::class, 'update']);
        Route::patch('users/{id}/toggle-active', [\App\Http\Controllers\Api\UserController::class, 'toggleActive']);
        Route::delete('users/{id}', [\App\Http\Controllers\Api\UserController::class, 'destroy']);
        
        // Gestion des catégories (admin uniquement)
        Route::get('categories', [CategoryController::class, 'index']);
        Route::get('categories/{category}', [CategoryController::class, 'show']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
        
        // Gestion des fournisseurs (admin uniquement)
        Route::get('suppliers', [SupplierController::class, 'index']);
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show']);
        Route::post('suppliers', [SupplierController::class, 'store']);
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update']);
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy']);
    });
    
    // Routes pour les opérations partagées (admin et magasinier mais avec des permissions différentes)
    // Ces opérations peuvent être effectuées par les deux rôles, mais les restrictions seront gérées au niveau des contrôleurs
    
    // Gestion des produits
    Route::post('products', [ProductController::class, 'store'])->middleware('role:admin');
    Route::put('products/{product}', [ProductController::class, 'update'])->middleware('role:admin');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->middleware('role:admin');
    
    // Gestion des mouvements de stock
    Route::post('stock-movements', [StockMovementController::class, 'store'])->middleware('role:admin,magasinier');
    Route::put('stock-movements/{stockMovement}', [StockMovementController::class, 'update'])->middleware('role:admin');
    Route::delete('stock-movements/{stockMovement}', [StockMovementController::class, 'destroy'])->middleware('role:admin');    // Gestion des bons d'entrée
    Route::post('entry-forms', [EntryFormController::class, 'store'])->middleware('role:admin,magasinier');
    Route::put('entry-forms/{entryForm}', [EntryFormController::class, 'update'])->middleware('role:admin');
    Route::delete('entry-forms/{entryForm}', [EntryFormController::class, 'destroy'])->middleware('role:admin');
      // Validation et gestion avancée des bons d'entrée
    Route::post('entry-forms/{entryForm}/validate', [EntryFormController::class, 'validate'])->middleware('role:admin,magasinier');
    Route::post('entry-forms/{entryForm}/cancel', [EntryFormController::class, 'cancel'])->middleware('role:admin');
    Route::get('entry-forms/{entryForm}/history', [EntryFormController::class, 'history'])->middleware('role:admin,magasinier');
    Route::post('entry-forms/check-duplicates', [EntryFormController::class, 'checkDuplicates'])->middleware('role:admin,magasinier');
    Route::get('entry-forms/{entryForm}/debug', [EntryFormController::class, 'debug'])->middleware('role:admin,magasinier');
    
    // Gestion des bons de sortie
    Route::post('exit-forms', [ExitFormController::class, 'store'])->middleware('role:admin,magasinier');
    Route::put('exit-forms/{exitForm}', [ExitFormController::class, 'update'])->middleware('role:admin');
    Route::delete('exit-forms/{exitForm}', [ExitFormController::class, 'destroy'])->middleware('role:admin');
    Route::post('exit-forms/{exitForm}/validate', [ExitFormController::class, 'validate'])->middleware('role:admin,magasinier');
    Route::post('exit-forms/{exitForm}/cancel', [ExitFormController::class, 'cancel'])->middleware('role:admin');
    Route::get('exit-forms/{exitForm}/history', [ExitFormController::class, 'history'])->middleware('role:admin,magasinier');
    Route::post('exit-forms/check-duplicates', [ExitFormController::class, 'checkDuplicates'])->middleware('role:admin,magasinier');
    
    // Routes pour le tableau de bord analytique
    Route::prefix('dashboard')->middleware('role:admin,magasinier')->group(function () {
        Route::get('summary', [DashboardController::class, 'summary']);
        Route::get('recent-movements', [DashboardController::class, 'recentMovements']);
        Route::get('category-analysis', [DashboardController::class, 'categoryAnalysis']);
        Route::get('stock-movement-chart', [DashboardController::class, 'stockMovementChart']);
    });
});
