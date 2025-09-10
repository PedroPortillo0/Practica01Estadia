<?php

namespace App\Application\UseCases;

use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\ValueObjects\Password;
use App\Models\VerificationCode;
use Exception;

class ResetPassword
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $email, string $code, string $newPassword): array
    {
        try {
            // Validar entrada
            if (empty($email) || empty($code) || empty($newPassword)) {
                throw new Exception('Email, código y nueva contraseña son requeridos');
            }

            if (strlen($newPassword) < 6) {
                throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
            }

            // Buscar usuario por email
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }

            // Buscar código válido para reset de contraseña
            $verificationCode = VerificationCode::findValidCode($user->getId(), $code, 'password_reset');
            if (!$verificationCode) {
                throw new Exception('Código inválido o expirado');
            }

            // Crear value object para la nueva contraseña
            $passwordObj = new Password($newPassword);
            $hashedPassword = $passwordObj->hash();

            // Marcar código como usado
            $verificationCode->markAsUsed();

            // Actualizar contraseña
            $this->userRepository->update($user->getId(), ['password' => $hashedPassword]);

            return [
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
