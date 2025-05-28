<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::paginate(10);
        return ApiResponse::success(
            UserResource::collection($users),
            'Liste des utilisateurs récupérée avec succès'
        );
    }

    
    public function show($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return ApiResponse::notFound('Utilisateur non trouvé');
        }
        
        return ApiResponse::success(
            new UserResource($user),
            'Utilisateur récupéré avec succès'
        );
    }

        public function update(UserRequest $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return ApiResponse::notFound('Utilisateur non trouvé');
        }
        
        // La validation est gérée par UserRequest
        
        // Mise à jour des champs si présents dans la requête
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->has('role')) {
            $user->role = $request->role;
        }
        
        if ($request->has('active')) {
            $user->active = $request->active;
        }
        
        $user->save();
        
        return ApiResponse::success(
            new UserResource($user),
            'Utilisateur mis à jour avec succès'
        );
    }

    
    public function toggleActive($id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return ApiResponse::notFound('Utilisateur non trouvé');
        }
        
        // Ne pas permettre de désactiver son propre compte admin
        if ($user->id === auth()->id() && $user->role === 'admin') {
            return ApiResponse::error('Vous ne pouvez pas désactiver votre propre compte administrateur', [], 403);
        }
        
        $user->active = !$user->active;
        $user->save();
        
        $status = $user->active ? 'activé' : 'désactivé';
        
        return ApiResponse::success(
            new UserResource($user),
            "Compte utilisateur $status avec succès"
        );
    }

   
   public function destroy($id)
   {
       $user = User::find($id);

       if (!$user) {
           return ApiResponse::notFound('Utilisateur non trouvé');
       }

       // Empêcher la suppression du compte de l'utilisateur actuellement authentifié
       if ($user->id === auth()->id()) {
           return ApiResponse::error('Vous ne pouvez pas supprimer votre propre compte.', [], 403);
       }

       $user->delete();

       return ApiResponse::success(
           null,
           'Utilisateur supprimé avec succès'
       );
   }
}
