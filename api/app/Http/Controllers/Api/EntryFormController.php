<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntryForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntryFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entries = EntryForm::with('supplier')->paginate(10);
        return response()->json($entries);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:255|unique:entry_forms,reference',
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'required|string',
            'total' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $entry = EntryForm::create($request->all());
        return response()->json($entry, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entry = EntryForm::with('supplier')->find($id);
        if (!$entry) {
            return response()->json(['message' => 'Bon d\'entrée non trouvé'], 404);
        }
        return response()->json($entry);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $entry = EntryForm::find($id);
        if (!$entry) {
            return response()->json(['message' => "Bon d'entrée non trouvé"], 404);
        }
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:255|unique:entry_forms,reference,' . $id,
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'status' => 'required|string',
            'total' => 'required|numeric|min:0',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $entry->update($request->all());
        return response()->json($entry);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entry = EntryForm::find($id);
        if (!$entry) {
            return response()->json(['message' => 'Bon d\'entrée non trouvé'], 404);
        }
        $entry->delete();
        return response()->json(['message' => 'Bon d\'entrée supprimé avec succès']);
    }
}
