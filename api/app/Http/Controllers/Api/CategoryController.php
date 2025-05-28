<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
        public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return (new CategoryCollection($categories))->additional([
            'success' => true,
            'message' => 'Liste des catégories récupérée avec succès'
        ]);
    }    public function store(CategoryRequest $request)
    {
        // La validation est déjà gérée par la classe CategoryRequest
        $category = Category::create($request->validated());
        return ApiResponse::success(new CategoryResource($category), 'Catégorie créée avec succès', 201);
    }

        public function show(string $id)
    {
        $category = Category::withCount('products')->find($id);
        if (!$category) {
            return ApiResponse::notFound('Catégorie non trouvée');
        }
        return ApiResponse::success(new CategoryResource($category), 'Catégorie récupérée avec succès');
    }    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::notFound('Catégorie non trouvée');
        }
        
        // La validation est déjà gérée par la classe CategoryRequest
        $category->update($request->validated());
        return ApiResponse::success(new CategoryResource($category), 'Catégorie mise à jour avec succès');
    }

        public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::notFound('Catégorie non trouvée');
        }
        $category->delete();
        return ApiResponse::success(null, 'Catégorie supprimée avec succès');
    }

        public function products($id)
    {
        $category = Category::with('products')->find($id);
        if (!$category) {
            return ApiResponse::notFound('Catégorie non trouvée');
        }
        return ApiResponse::success(ProductResource::collection($category->products), 'Produits de la catégorie récupérés avec succès');
    }
}
