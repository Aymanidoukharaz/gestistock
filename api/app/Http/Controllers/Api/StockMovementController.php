<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockMovementRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\StockMovementCollection;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $movements = StockMovement::with(['product', 'product.category', 'user'])->paginate(10);
        return (new StockMovementCollection($movements))->additional([
            'success' => true,
            'message' => 'Liste des mouvements de stock récupérée avec succès'
        ]);
    }/**
     * Store a newly created resource in storage.
     */    public function store(StockMovementRequest $request)
    {
        // La validation est déjà gérée par la classe StockMovementRequest
        $movement = StockMovement::create($request->validated());
        $movement->load(['product', 'product.category', 'user']);
        return ApiResponse::success(new StockMovementResource($movement), 'Mouvement de stock créé avec succès', 201);
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $movement = StockMovement::with(['product', 'product.category', 'user'])->find($id);
        if (!$movement) {
            return ApiResponse::notFound('Mouvement non trouvé');
        }
        return ApiResponse::success(new StockMovementResource($movement), 'Mouvement de stock récupéré avec succès');
    }/**
     * Update the specified resource in storage.
     */    public function update(StockMovementRequest $request, string $id)
    {
        $movement = StockMovement::find($id);
        if (!$movement) {
            return ApiResponse::notFound('Mouvement non trouvé');
        }
        
        // La validation est déjà gérée par la classe StockMovementRequest
        $movement->update($request->validated());
        $movement->load(['product', 'product.category', 'user']);
        return ApiResponse::success(new StockMovementResource($movement), 'Mouvement de stock mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $movement = StockMovement::find($id);
        if (!$movement) {
            return ApiResponse::notFound('Mouvement non trouvé');
        }
        $movement->delete();
        return ApiResponse::success(null, 'Mouvement supprimé avec succès');
    }

    /**
     * Retourne les mouvements de stock d'un produit donné.
     */    public function productMovements($productId)
    {
        $movements = StockMovement::with(['product', 'product.category', 'user'])
            ->where('product_id', $productId)
            ->orderBy('date', 'desc')
            ->get();
        return ApiResponse::success(StockMovementResource::collection($movements), 'Mouvements de stock du produit récupérés avec succès');
    }
}
