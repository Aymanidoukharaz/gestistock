<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StockMovementRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\StockMovementCollection;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\Models\Product; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added
use Illuminate\Support\Facades\Log; // Added for logging errors

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
        $validatedData = $request->validated();

        DB::beginTransaction();

        try {
            // 1. Create the StockMovement
            $movement = StockMovement::create($validatedData);

            // 2. Fetch the related Product
            $product = Product::findOrFail($validatedData['product_id']);

            // 3. Update Product quantity
            if ($validatedData['type'] === 'entry') {
                $product->quantity += $validatedData['quantity'];
            } elseif ($validatedData['type'] === 'exit') {
                // Ensure stock doesn't go negative if not allowed, or handle as per business rules
                // For now, we'll assume it can go negative or this is handled by validation/frontend
                $product->quantity -= $validatedData['quantity'];
            }

            // 4. Save the Product
            $product->save();

            // 5. Commit the transaction
            DB::commit();

            $movement->load(['product', 'product.category', 'user']);
            return ApiResponse::success(new StockMovementResource($movement), 'Mouvement de stock créé et quantité produit mise à jour avec succès', 201);

        } catch (\Exception $e) {
            // 6. Rollback transaction on error
            DB::rollBack();
            Log::error("Error creating stock movement or updating product quantity: " . $e->getMessage());
            return ApiResponse::error('Erreur lors de la création du mouvement de stock ou de la mise à jour de la quantité du produit: ' . $e->getMessage(), 500);
        }
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
     * Display a listing of the stock movements for a given product.
     */
    public function getByProduct(Product $product)
    {
        $movements = $product->stockMovements()->orderBy('date', 'desc')->get();
        // It's good practice to load related models if they are consistently used in the resource
        // and not automatically loaded by the relationship or resource.
        // Example: $movements->load(['user']); // if user is needed and not always loaded.
        // For now, assuming StockMovementResource handles necessary relations or they are eager loaded.

        return ApiResponse::success(StockMovementResource::collection($movements), 'Mouvements de stock pour le produit récupérés avec succès');
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
