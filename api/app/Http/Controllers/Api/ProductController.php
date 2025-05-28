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
        public function index()
    {
        $products = Product::with('category')->paginate(10);
        return (new ProductCollection($products))->additional([
            'success' => true,
            'message' => 'Liste des produits récupérée avec succès'
        ]);
    }    public function store(ProductRequest $request)
    {
        // La validation est déjà gérée par la classe ProductRequest
        $product = Product::create($request->validated());
        return ApiResponse::success(new ProductResource($product), 'Produit créé avec succès', 201);
    }

        public function show(string $id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return ApiResponse::notFound('Produit non trouvé');
        }
        return ApiResponse::success(new ProductResource($product), 'Produit récupéré avec succès');
    }    public function update(ProductRequest $request, string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::notFound('Produit non trouvé');
        }
        
        // La validation est déjà gérée par la classe ProductRequest
        $product->update($request->validated());
        return ApiResponse::success(new ProductResource($product), 'Produit mis à jour avec succès');
    }

        public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::notFound('Produit non trouvé');
        }
        $product->delete();
        return ApiResponse::success(null, 'Produit supprimé avec succès');
    }

        public function lowStock()
    {
        $products = Product::with('category')->whereColumn('quantity', '<=', 'min_stock')->get();
        return ApiResponse::success(ProductResource::collection($products), 'Liste des produits en stock faible récupérée avec succès');
    }

    
    public function categories()
    {
        $categories = \App\Models\Category::withCount('products')->get();
        return ApiResponse::success(\App\Http\Resources\CategoryResource::collection($categories), 'Liste des catégories de produits récupérée avec succès');
    }
}
