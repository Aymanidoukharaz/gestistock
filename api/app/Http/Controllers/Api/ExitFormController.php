<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExitForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExitFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exits = ExitForm::paginate(10);
        return response()->json($exits);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:255|unique:exit_forms,reference',
            'date' => 'required|date',
            'destination' => 'required|string|max:255',
            'status' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $exit = ExitForm::create($request->all());
        return response()->json($exit, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return response()->json(['message' => 'Bon de sortie non trouvé'], 404);
        }
        return response()->json($exit);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return response()->json(['message' => 'Bon de sortie non trouvé'], 404);
        }
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string|max:255|unique:exit_forms,reference,' . $id,
            'date' => 'required|date',
            'destination' => 'required|string|max:255',
            'status' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $exit->update($request->all());
        return response()->json($exit);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $exit = ExitForm::find($id);
        if (!$exit) {
            return response()->json(['message' => 'Bon de sortie non trouvé'], 404);
        }
        $exit->delete();
        return response()->json(['message' => 'Bon de sortie supprimé avec succès']);
    }
}
