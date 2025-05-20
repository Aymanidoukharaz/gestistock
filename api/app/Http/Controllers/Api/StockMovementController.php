<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockMovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $movements = StockMovement::with(['product', 'product.category'])->paginate(10);
        return response()->json($movements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrée,sortie',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $movement = StockMovement::create($request->all());
        return response()->json($movement, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $movement = StockMovement::with(['product', 'product.category'])->find($id);
        if (!$movement) {
            return response()->json(['message' => 'Mouvement non trouvé'], 404);
        }
        return response()->json($movement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $movement = StockMovement::find($id);
        if (!$movement) {
            return response()->json(['message' => 'Mouvement non trouvé'], 404);
        }
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:entrée,sortie',
            'quantity' => 'required|integer|min:1',
            'date' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $movement->update($request->all());
        return response()->json($movement);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $movement = StockMovement::find($id);
        if (!$movement) {
            return response()->json(['message' => 'Mouvement non trouvé'], 404);
        }
        $movement->delete();
        return response()->json(['message' => 'Mouvement supprimé avec succès']);
    }

    /**
     * Retourne les mouvements de stock d'un produit donné.
     */
    public function productMovements($productId)
    {
        $movements = StockMovement::with(['product', 'product.category'])
            ->where('product_id', $productId)
            ->orderBy('date', 'desc')
            ->get();
        return response()->json($movements);
    }
}
