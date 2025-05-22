<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $products = Product::with('category')->paginate(10);
        return (new ProductCollection($products))->additional([
            'success' => true,
            'message' => 'Liste des produits récupérée avec succès'
        ]);
    }/**
     * Store a newly created resource in storage.
     */    public function store(ProductRequest $request)
    {
        // La validation est déjà gérée par la classe ProductRequest
        $product = Product::create($request->validated());
        return ApiResponse::success(new ProductResource($product), 'Produit créé avec succès', 201);
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return ApiResponse::notFound('Produit non trouvé');
        }
        return ApiResponse::success(new ProductResource($product), 'Produit récupéré avec succès');
    }/**
     * Update the specified resource in storage.
     */    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::notFound('Produit non trouvé');
        }
        
        // La validation est déjà gérée par la classe ProductRequest
        $product->update($request->validated());
        return ApiResponse::success(new ProductResource($product), 'Produit mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::notFound('Produit non trouvé');
        }
        $product->delete();
        return ApiResponse::success(null, 'Produit supprimé avec succès');
    }

    /**
     * Retourne les produits en stock faible (quantité <= min_stock).
     */    public function lowStock()
    {
        $products = Product::with('category')->whereColumn('quantity', '<=', 'min_stock')->get();
        return ApiResponse::success(ProductResource::collection($products), 'Liste des produits en stock faible récupérée avec succès');
    }
}
