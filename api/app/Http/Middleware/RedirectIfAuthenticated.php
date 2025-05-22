<?php

namespace App\Http\Middleware;

use App\Http\Resources\ApiResponse;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Si c'est une requête API, renvoyer une réponse JSON
                if ($request->is('api/*') || $request->expectsJson()) {
                    return ApiResponse::error('Vous êtes déjà authentifié', [], 400);
                }
                
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
