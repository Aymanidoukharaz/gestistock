<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{    /**
     * Créer un nouveau contrôleur d'authentification
     *
     * @return void
     */
    public function __construct()
    {
        // Le middleware est maintenant appliqué au niveau des routes dans routes/api.php
    }

    /**
     * Enregistrer un nouvel utilisateur.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */    public function register(\App\Http\Requests\UserRequest $request)
    {
        // La validation est gérée par UserRequest

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'active' => $request->input('active', true),
        ]);
        
        return ApiResponse::success(new UserResource($user), 'Utilisateur créé avec succès', 201);
    }

    /**
     * Obtenir un JWT via les identifiants donnés.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);        $credentials = $request->only('email', 'password');

        if (! $token = Auth::attempt($credentials)) {
            return ApiResponse::unauthenticated('Identifiants invalides');
        }

        return $this->respondWithToken($token);
    }

    /**
     * Obtenir l'utilisateur authentifié.
     *
     * @return \Illuminate\Http\JsonResponse
     */    public function me()
    {
        return ApiResponse::success(new UserResource(Auth::user()), 'Informations utilisateur récupérées avec succès');
    }

    /**
     * Déconnecter l'utilisateur (invalider le token).
     *
     * @return \Illuminate\Http\JsonResponse
     */    public function logout()
    {
        Auth::logout();

        return ApiResponse::success(null, 'Déconnecté avec succès');
    }

    /**
     * Rafraîchir un token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Obtenir la structure de tableau du token.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */    protected function respondWithToken($token)
    {
        return ApiResponse::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => new UserResource(Auth::user())
        ], 'Authentification réussie');
    }
}
