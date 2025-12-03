<?php

namespace App\Http\Controllers;

use App\Application\UseCases\GetDailyQuote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyQuoteController extends Controller
{
    public function __construct(
        private GetDailyQuote $getDailyQuote
    ) {}

    /**
     * Obtiene la frase del día (versión simple para dashboard)
     * Si el usuario está autenticado y tiene quiz completo, devuelve frase personalizada
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDailyQuote(Request $request): JsonResponse
    {
        // Intentar obtener usuario autenticado (puede ser null si no está autenticado)
        $user = $request->attributes->get('authenticated_user');
        $userId = $user ? $user->getId() : null;

        $result = $this->getDailyQuote->execute(includeDetail: false, userId: $userId);
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Obtiene el detalle completo de la frase del día
     * Si el usuario está autenticado y tiene quiz completo, devuelve frase personalizada
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getDailyQuoteDetail(Request $request): JsonResponse
    {
        // Intentar obtener usuario autenticado (puede ser null si no está autenticado)
        $user = $request->attributes->get('authenticated_user');
        $userId = $user ? $user->getId() : null;

        $result = $this->getDailyQuote->execute(includeDetail: true, userId: $userId);
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Obtiene todas las frases (para testing o admin)
     * 
     * @return JsonResponse
     */
    public function getAllQuotes(): JsonResponse
    {
        $result = $this->getDailyQuote->getAllQuotes();
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }
}

