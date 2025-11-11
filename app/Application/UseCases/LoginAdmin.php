<?php

namespace App\Application\UseCases;

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\Ports\TokenServiceInterface;
use Exception;

class LoginAdmin
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $tokenService
    ) {}

    public function execute(array $loginData): array
    {
        try {
            $email = $loginData['email'] ?? '';
            $password = $loginData['password'] ?? '';

            // Validar que se proporcionen email y contraseña
            if (empty($email) || empty($password)) {
                throw new Exception('Email y contraseña son requeridos');
            }

            // Validar formato de email
            $emailObj = new Email($email);

            // Buscar usuario por email
            $user = $this->userRepository->findByEmail($emailObj->getValue());
            if (!$user) {
                throw new Exception('Credenciales inválidas');
            }

            // Verificar que el usuario sea administrador
            if (!$user->isAdmin()) {
                throw new Exception('No tienes permisos de administrador');
            }

            // Verificar que el email esté verificado
            if (!$user->isEmailVerificado()) {
                throw new Exception('Por favor verifica tu email antes de iniciar sesión');
            }

            // Verificar contraseña
            if (!Password::verify($password, $user->getPassword())) {
                throw new Exception('Credenciales inválidas');
            }

            // Generar token de autenticación
            $token = $this->tokenService->generateToken([
                'user_id' => $user->getId(),
                'email' => $user->getEmail()
            ]);

            return [
                'success' => true,
                'message' => 'Login exitoso',
                'token' => $token,
                'data' => $user->toArray()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

