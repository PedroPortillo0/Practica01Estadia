<?php

namespace App\Http\Controllers;

use App\Application\UseCases\SubmitUserQuiz;
use App\Application\UseCases\GetUserQuiz;
use App\Application\UseCases\GetQuizOptions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class QuizController extends Controller
{
    public function __construct(
        private SubmitUserQuiz $submitUserQuiz,
        private GetUserQuiz $getUserQuiz,
        private GetQuizOptions $getQuizOptions
    ) {}

    /**
     * Obtener todas las opciones disponibles para el quiz
     */
    public function getOptions(): JsonResponse
    {
        $result = $this->getQuizOptions->execute();
        return response()->json($result, 200);
    }

    /**
     * Enviar respuestas del quiz
     */
    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'age_range' => 'required|string',
            'gender' => 'required|string',
            'sexual_orientation' => 'required|string',
            'state' => 'nullable|string',
            'religious_belief' => 'required|string',
            'spiritual_practice_level' => 'required|string',
            'spiritual_practice_frequency' => 'required|string',
            'stoic_values' => 'required|array|min:2',
            'stoic_values.*' => 'string',
            'life_purpose' => 'required|string',
            'happiness_source' => 'required|string',
            'adversity_response' => 'required|string',
            'life_development_area' => 'required|string',
        ], [
            'age_range.required' => 'El rango de edad es requerido',
            'gender.required' => 'El género es requerido',
            'sexual_orientation.required' => 'La orientación sexual es requerida',
            'religious_belief.required' => 'La creencia religiosa es requerida',
            'spiritual_practice_level.required' => 'El nivel de práctica espiritual es requerido',
            'spiritual_practice_frequency.required' => 'La frecuencia de práctica espiritual es requerida',
            'stoic_values.required' => 'Los valores estoicos son requeridos',
            'stoic_values.min' => 'Debes seleccionar al menos 2 valores estoicos',
            'life_purpose.required' => 'El propósito de vida es requerido',
            'happiness_source.required' => 'La fuente de felicidad es requerida',
            'adversity_response.required' => 'La respuesta a adversidades es requerida',
            'life_development_area.required' => 'El área de desarrollo es requerida',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $userId = $this->getAuthenticatedUserId($request);
        $result = $this->submitUserQuiz->execute($userId, $request->all());

        return response()->json($result, $result['success'] ? 201 : 400);
    }

    /**
     * Obtener el quiz del usuario autenticado
     */
    public function getUserQuizResponse(Request $request): JsonResponse
    {
        $userId = $this->getAuthenticatedUserId($request);
        $result = $this->getUserQuiz->execute($userId);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Obtener usuario autenticado desde el middleware
     */
    private function getAuthenticatedUserId(Request $request): string
    {
        $user = $request->attributes->get('authenticated_user');
        if (!$user) {
            throw new \Exception('Usuario no autenticado');
        }
        return $user->getId();
    }
}
