<?php

namespace App\Application\UseCases;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\Password;
use App\Domain\Ports\UserRepositoryInterface;
use App\Domain\Ports\EmailServiceInterface;
use App\Domain\Ports\TokenServiceInterface;
use Illuminate\Support\Str;
use Exception;

class RegisterUser
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private EmailServiceInterface $emailService,
        private TokenServiceInterface $tokenService
    ) {}

    public function execute(array $userData): array
    {
        try {
            // Validar datos de entrada
            $errores = User::validarDatos($userData);
            if (!empty($errores)) {
                throw new Exception('Errores de validación: ' . implode(', ', $errores));
            }

            // Verificar que el email no exista
            if ($this->userRepository->exists($userData['email'])) {
                throw new Exception('El email ya está registrado');
            }

            // Crear value objects
            $email = new Email($userData['email']);
            $password = new Password($userData['password']);

            // Hashear contraseña
            $hashedPassword = $password->hash();

            // Crear usuario
            $user = new User(
                Str::uuid()->toString(),
                trim($userData['nombre']),
                trim($userData['apellidoPaterno']),
                trim($userData['apellidoMaterno']),
                $userData['telefono'],
                $email->getValue(),
                $hashedPassword,
                false // email no verificado inicialmente
            );

            // Guardar usuario
            $savedUser = $this->userRepository->save($user);

            // Generar código de verificación de 6 dígitos
            $verificationCode = \App\Models\VerificationCode::createForUser($savedUser->getId());

            // Enviar email de verificación (usando código plano)
            $this->emailService->sendVerificationEmail($savedUser->getEmail(), $verificationCode->getPlainCode());

            return [
                'success' => true,
                'message' => 'Usuario registrado exitosamente. Por favor verifica tu email.',
                'data' => $savedUser->toArray()
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
