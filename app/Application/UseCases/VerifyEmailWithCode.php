<?php

namespace App\Application\UseCases;

use App\Domain\Ports\UserRepositoryInterface;
use App\Models\VerificationCode;
use Exception;

class VerifyEmailWithCode
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $userId, string $code): array
    {
        try {
            // Buscar el usuario
            $user = $this->userRepository->findById($userId);
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

            // Buscar código válido
            $verificationCode = VerificationCode::findValidCode($userId, $code);
            if (!$verificationCode) {
                throw new Exception('Código inválido o expirado');
            }

            // Marcar código como usado
            $verificationCode->markAsUsed();

            // Marcar email como verificado
            $this->userRepository->update($user->getId(), ['email_verificado' => true]);

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
