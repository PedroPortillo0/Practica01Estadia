<?php

namespace App\Application\UseCases;

use App\Models\UserQuizResponse;
use Illuminate\Support\Str;
use Exception;

class SubmitUserQuiz
{
    public function execute(string $userId, array $quizData): array
    {
        try {
            // Verificar si el usuario ya completó el quiz
            $existingResponse = UserQuizResponse::where('user_id', $userId)->first();
            if ($existingResponse) {
                throw new Exception('Ya has completado el quiz anteriormente. No es posible realizarlo de nuevo.');
            }

            // Validar que todos los campos requeridos estén presentes
            $this->validateQuizData($quizData);

            // Crear la respuesta del quiz
            $quizResponse = UserQuizResponse::create([
                'id' => Str::uuid()->toString(),
                'user_id' => $userId,
                'age_range' => $quizData['age_range'],
                'gender' => $quizData['gender'],
                'sexual_orientation' => $quizData['sexual_orientation'],
                'state' => $quizData['state'] ?? null,
                'religious_belief' => $quizData['religious_belief'],
                'spiritual_practice_level' => $quizData['spiritual_practice_level'],
                'spiritual_practice_frequency' => $quizData['spiritual_practice_frequency'],
                'stoic_values' => $quizData['stoic_values'],
                'life_purpose' => $quizData['life_purpose'],
                'happiness_source' => $quizData['happiness_source'],
                'adversity_response' => $quizData['adversity_response'],
                'life_development_area' => $quizData['life_development_area'],
                'completed_at' => now()
            ]);

            return [
                'success' => true,
                'message' => 'Quiz completado exitosamente',
                'data' => $quizResponse
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function validateQuizData(array $data): void
    {
        $requiredFields = [
            'age_range',
            'gender',
            'sexual_orientation',
            'religious_belief',
            'spiritual_practice_level',
            'spiritual_practice_frequency',
            'stoic_values',
            'life_purpose',
            'happiness_source',
            'adversity_response',
            'life_development_area'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("El campo '{$field}' es requerido");
            }
        }

        // Validar que stoic_values sea un array y tenga al menos 2 elementos
        if (!is_array($data['stoic_values']) || count($data['stoic_values']) < 2) {
            throw new Exception('Debes seleccionar al menos 2 valores estoicos');
        }
    }
}
