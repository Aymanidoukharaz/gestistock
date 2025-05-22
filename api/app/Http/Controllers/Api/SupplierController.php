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
    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $suppliers = Supplier::withCount('entryForms')->paginate(10);
        return (new SupplierCollection($suppliers))->additional([
            'success' => true,
            'message' => 'Liste des fournisseurs récupérée avec succès'
        ]);
    }/**
     * Store a newly created resource in storage.
     */    public function store(SupplierRequest $request)
    {
        // La validation est déjà gérée par la classe SupplierRequest
        $supplier = Supplier::create($request->validated());
        return ApiResponse::success(new SupplierResource($supplier), 'Fournisseur créé avec succès', 201);
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $supplier = Supplier::withCount('entryForms')->find($id);
        if (!$supplier) {
            return ApiResponse::notFound('Fournisseur non trouvé');
        }
        return ApiResponse::success(new SupplierResource($supplier), 'Fournisseur récupéré avec succès');
    }/**
     * Update the specified resource in storage.
     */    public function update(SupplierRequest $request, string $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return ApiResponse::notFound('Fournisseur non trouvé');
        }
        
        // La validation est déjà gérée par la classe SupplierRequest
        $supplier->update($request->validated());
        return ApiResponse::success(new SupplierResource($supplier), 'Fournisseur mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return ApiResponse::notFound('Fournisseur non trouvé');
        }
        $supplier->delete();
        return ApiResponse::success(null, 'Fournisseur supprimé avec succès');
    }
}
