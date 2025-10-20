<?php

namespace App\Http\Controllers;

use App\Application\UseCases\GetDailyQuote;
use Illuminate\Http\JsonResponse;

class DailyQuoteController extends Controller
{
    public function __construct(
        private GetDailyQuote $getDailyQuote
    ) {}

    /**
     * Obtiene la frase del día (versión simple para dashboard)
     * 
     * @return JsonResponse
     */
    public function getDailyQuote(): JsonResponse
    {
        $result = $this->getDailyQuote->execute(includeDetail: false);
        
        return response()->json($result, $result['success'] ? 200 : 500);
    }

    /**
     * Obtiene el detalle completo de la frase del día
     * 
     * @return JsonResponse
     */
    public function getDailyQuoteDetail(): JsonResponse
    {
        $result = $this->getDailyQuote->execute(includeDetail: true);
        
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

