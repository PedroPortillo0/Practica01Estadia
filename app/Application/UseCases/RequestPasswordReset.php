<?php

namespace App\Application\UseCases;

use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\Ports\EmailServiceInterface;
use App\Models\VerificationCode;
use Exception;

class RequestPasswordReset
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EmailServiceInterface $emailService
    ) {}

    public function execute(string $email): array
    {
        try {
            // Validar formato de email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email debe tener un formato válido');
            }

            // Buscar usuario por email
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                // Por seguridad, no revelamos si el email existe o no
                return [
                    'success' => true,
                    'message' => 'Si el email existe, recibirás un código de verificación.'
                ];
            }

            // Verificar que el email esté verificado
            if (!$user->isEmailVerificado()) {
                throw new Exception('El email debe estar verificado para cambiar la contraseña');
            }

            // Generar código de verificación para reset de contraseña
            $verificationCode = VerificationCode::createForUser($user->getId(), 'password_reset');

            // Enviar email con código (usando código plano)
            $this->emailService->sendPasswordResetEmail($user->getEmail(), $verificationCode->getPlainCode());

            return [
                'success' => true,
                'message' => 'Código de verificación enviado a tu email'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
