<?php

namespace App\Http\Resources;

class ApiResponse
{
    
    public static function success($data = null, string $message = 'Opération réussie', int $status = 200)
    {
        $responseData = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        error_log("[DEBUG] ApiResponse::success BEFORE json_encode - Output buffer: " . ob_get_contents());
        error_log("[DEBUG] ApiResponse::success data: " . json_encode($responseData));
        return response()->json($responseData, $status);
    }

    
    public static function error(string $message = 'Une erreur est survenue', array $errors = [], int $status = 400)
    {
        $responseData = [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
        error_log("[DEBUG] ApiResponse::error BEFORE json_encode - Output buffer: " . ob_get_contents());
        error_log("[DEBUG] ApiResponse::error data: " . json_encode($responseData));
        return response()->json($responseData, $status);
    }

    
    public static function notFound(string $message = 'Ressource non trouvée')
    {
        return self::error($message, [], 404);
    }

    
    public static function validationError(array $errors)
    {
        return self::error('Les données fournies sont invalides', $errors, 422);
    }

    
    public static function unauthorized(string $message = 'Non autorisé')
    {
        return self::error($message, [], 403);
    }

    
    public static function unauthenticated(string $message = 'Authentification requise')
    {
        return self::error($message, [], 401);
    }
}
