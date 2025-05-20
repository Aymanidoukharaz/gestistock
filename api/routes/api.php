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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes d'authentification
Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('user', [AuthController::class, 'me']);
});


// Routes pour les catégories
Route::apiResource('categories', CategoryController::class);

// Routes pour les produits
Route::apiResource('products', ProductController::class);

// Routes pour les fournisseurs
Route::apiResource('suppliers', SupplierController::class);

// Routes pour les mouvements de stock
Route::apiResource('stock-movements', StockMovementController::class);

// Routes pour les bons d'entrée
Route::apiResource('entry-forms', EntryFormController::class);

// Routes pour les bons de sortie
Route::apiResource('exit-forms', ExitFormController::class);

// Ajoute ici les routes spécifiques si besoin
// Exemple : Route::get('products/low-stock', [ProductController::class, 'lowStock']);
