<?php

namespace App\Application\UseCases;

use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\Ports\TokenServiceInterface;
use Exception;

class VerifyEmail
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $tokenService
    ) {}

    public function execute(string $token): array
    {
        try {
            // Verificar token
            $decoded = $this->tokenService->verifyToken($token);
            
            if (!isset($decoded['user_id'])) {
                throw new Exception('Token inválido o expirado');
            }

            // Buscar usuario
            $user = $this->userRepository->findById($decoded['user_id']);
            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }

            // Verificar si el email ya está verificado
            if ($user->isEmailVerificado()) {
                return [
                    'success' => true,
                    'message' => 'El email ya ha sido verificado previamente'
                ];
            }

            // Marcar email como verificado
            $this->userRepository->update($user->getId(), ['emailVerificado' => true]);

            return [
                'success' => true,
                'message' => 'Email verificado exitosamente'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
