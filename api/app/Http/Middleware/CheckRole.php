<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Vérifie si l'utilisateur a le rôle requis pour accéder à la ressource.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $roles  Le ou les rôles autorisés ('admin', 'magasinier', etc.)
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour accéder à cette ressource'
            ], 401);
        }

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Vérifier si l'utilisateur est actif
        if (!$user->active) {
            return response()->json([
                'success' => false,
                'message' => 'Votre compte a été désactivé'
            ], 403);
        }

        // Si aucun rôle n'est spécifié, on autorise l'accès
        if (empty($roles)) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a l'un des rôles requis
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // L'utilisateur n'a pas les autorisations nécessaires
        return response()->json([
            'success' => false,
            'message' => 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette ressource'
        ], 403);
    }
}
