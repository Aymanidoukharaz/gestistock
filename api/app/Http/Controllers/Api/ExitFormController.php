<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExitFormRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\ExitFormCollection;
use App\Http\Resources\ExitFormResource;
use App\Models\ExitForm;
use Illuminate\Http\Request;

class ExitFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $exits = ExitForm::with(['user', 'exitItems', 'exitItems.product'])->paginate(10);
        return (new ExitFormCollection($exits))->additional([
            'success' => true,
            'message' => 'Liste des bons de sortie récupérée avec succès'
        ]);
    }/**
     * Store a newly created resource in storage.
     */    public function store(ExitFormRequest $request)
    {
        // La validation est déjà gérée par la classe ExitFormRequest
        $exit = ExitForm::create($request->validated());
        $exit->load(['user', 'exitItems', 'exitItems.product']);
        return ApiResponse::success(new ExitFormResource($exit), 'Bon de sortie créé avec succès', 201);
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $exit = ExitForm::with(['user', 'exitItems', 'exitItems.product'])->find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        return ApiResponse::success(new ExitFormResource($exit), 'Bon de sortie récupéré avec succès');
    }/**
     * Update the specified resource in storage.
     */    public function update(ExitFormRequest $request, string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        
        // La validation est déjà gérée par la classe ExitFormRequest
        $exit->update($request->validated());
        $exit->load(['user', 'exitItems', 'exitItems.product']);
        return ApiResponse::success(new ExitFormResource($exit), 'Bon de sortie mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return ApiResponse::notFound('Bon de sortie non trouvé');
        }
        $exit->delete();
        return ApiResponse::success(null, 'Bon de sortie supprimé avec succès');
    }
}
