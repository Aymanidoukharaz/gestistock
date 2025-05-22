<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntryFormRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\EntryFormCollection;
use App\Http\Resources\EntryFormResource;
use App\Models\EntryForm;
use Illuminate\Http\Request;

class EntryFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */    public function index()
    {
        $entries = EntryForm::with(['supplier', 'user', 'entryItems', 'entryItems.product'])->paginate(10);
        return (new EntryFormCollection($entries))->additional([
            'success' => true,
            'message' => 'Liste des bons d\'entrée récupérée avec succès'
        ]);
    }/**
     * Store a newly created resource in storage.
     */    public function store(EntryFormRequest $request)
    {
        // La validation est déjà gérée par la classe EntryFormRequest
        $entry = EntryForm::create($request->validated());
        $entry->load(['supplier', 'user', 'entryItems', 'entryItems.product']);
        return ApiResponse::success(new EntryFormResource($entry), 'Bon d\'entrée créé avec succès', 201);
    }

    /**
     * Display the specified resource.
     */    public function show(string $id)
    {
        $entry = EntryForm::with(['supplier', 'user', 'entryItems', 'entryItems.product'])->find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        return ApiResponse::success(new EntryFormResource($entry), 'Bon d\'entrée récupéré avec succès');
    }/**
     * Update the specified resource in storage.
     */    public function update(EntryFormRequest $request, string $id)
    {
        $entry = EntryForm::find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        
        // La validation est déjà gérée par la classe EntryFormRequest
        $entry->update($request->validated());
        $entry->load(['supplier', 'user', 'entryItems', 'entryItems.product']);
        return ApiResponse::success(new EntryFormResource($entry), 'Bon d\'entrée mis à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */    public function destroy(string $id)
    {
        $entry = EntryForm::find($id);
        if (!$entry) {
            return ApiResponse::notFound('Bon d\'entrée non trouvé');
        }
        $entry->delete();
        return ApiResponse::success(null, 'Bon d\'entrée supprimé avec succès');
    }
}
