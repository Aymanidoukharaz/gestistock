<?php

namespace App\Exceptions;

use App\Http\Resources\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler 
{
    

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Gestion des exceptions pour les routes non trouvées
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::notFound('La ressource demandée n\'existe pas');
            }
        });

        // Gestion des exceptions pour les modèles non trouvés
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::notFound('La ressource demandée n\'existe pas');
            }
        });

        // Gestion des exceptions pour les validations
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::validationError($e->errors());
            }
        });

        // Gestion des exceptions pour l'authentification
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthenticated('Authentification requise');
            }
        });

        // Gestion des exceptions pour l'autorisation
        $this->renderable(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::unauthorized('Vous n\'êtes pas autorisé à effectuer cette action');
            }
        });

        // Gestion de toutes les autres exceptions non gérées
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*') && app()->environment('production')) {
                return ApiResponse::error('Une erreur est survenue', [], 500);
            }
        });
    }
}
