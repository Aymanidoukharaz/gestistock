<?php

namespace App\Http\Resources;

class ApiResponse
{
    /**
     * Retourne une réponse de succès formatée
     *
     * @param mixed $data
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, string $message = 'Opération réussie', int $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Retourne une réponse d'erreur formatée
     *
     * @param string $message
     * @param array $errors
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error(string $message = 'Une erreur est survenue', array $errors = [], int $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Retourne une réponse pour des données non trouvées
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function notFound(string $message = 'Ressource non trouvée')
    {
        return self::error($message, [], 404);
    }

    /**
     * Retourne une réponse pour des données validées invalides
     *
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public static function validationError(array $errors)
    {
        return self::error('Les données fournies sont invalides', $errors, 422);
    }

    /**
     * Retourne une réponse pour une action non autorisée
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthorized(string $message = 'Non autorisé')
    {
        return self::error($message, [], 403);
    }

    /**
     * Retourne une réponse pour une authentification requise
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public static function unauthenticated(string $message = 'Authentification requise')
    {
        return self::error($message, [], 401);
    }
}
