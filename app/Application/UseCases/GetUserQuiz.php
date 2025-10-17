<?php

namespace App\Application\UseCases;

use App\Models\UserQuizResponse;
use Exception;

class GetUserQuiz
{
    public function execute(string $userId): array
    {
        try {
            $quizResponse = UserQuizResponse::where('user_id', $userId)->first();

            if (!$quizResponse) {
                return [
                    'success' => true,
                    'message' => 'El usuario aún no ha completado el quiz',
                    'data' => null,
                    'has_completed' => false
                ];
            }

            return [
                'success' => true,
                'message' => 'Quiz recuperado exitosamente',
                'data' => $quizResponse,
                'has_completed' => true
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
