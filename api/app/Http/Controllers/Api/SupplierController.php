<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\SupplierCollection;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
        public function index()
    {
        $suppliers = Supplier::withCount('entryForms')->paginate(10);
        return (new SupplierCollection($suppliers))->additional([
            'success' => true,
            'message' => 'Liste des fournisseurs récupérée avec succès'
        ]);
    }    public function store(SupplierRequest $request)
    {
        // La validation est déjà gérée par la classe SupplierRequest
        $supplier = Supplier::create($request->validated());
        return ApiResponse::success(new SupplierResource($supplier), 'Fournisseur créé avec succès', 201);
    }

        public function show(string $id)
    {
        $supplier = Supplier::withCount('entryForms')->find($id);
        if (!$supplier) {
            return ApiResponse::notFound('Fournisseur non trouvé');
        }
        return ApiResponse::success(new SupplierResource($supplier), 'Fournisseur récupéré avec succès');
    }    public function update(SupplierRequest $request, string $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return ApiResponse::notFound('Fournisseur non trouvé');
        }
        
        // La validation est déjà gérée par la classe SupplierRequest
        $supplier->update($request->validated());
        return ApiResponse::success(new SupplierResource($supplier), 'Fournisseur mis à jour avec succès');
    }

        public function destroy(string $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return ApiResponse::notFound('Fournisseur non trouvé');
        }
        $supplier->delete();
        return ApiResponse::success(null, 'Fournisseur supprimé avec succès');
    }
}
