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

            // Verificar si el email ya existe
            $existingUser = $this->userRepository->findByEmail($userData['email']);
            
            // Si el usuario existe y su email está verificado, rechazar el registro
            if ($existingUser && $existingUser->isEmailVerificado()) {
                throw new Exception('El email ya está registrado y verificado');
            }

            // Crear value objects
            $email = new Email($userData['email']);
            $password = new Password($userData['password']);

            // Hashear contraseña
            $hashedPassword = $password->hash();

            // Si el usuario existe pero NO está verificado, actualizarlo directamente
            if ($existingUser && !$existingUser->isEmailVerificado()) {
                // Actualizar usuario existente con los nuevos datos usando el método update
                $savedUser = $this->userRepository->update($existingUser->getId(), [
                    'nombre' => trim($userData['nombre']),
                    'apellidos' => trim($userData['apellidos']),
                    'email' => $email->getValue(),
                    'password' => $hashedPassword,
                    'email_verificado' => false
                ]);
                
                if (!$savedUser) {
                    throw new Exception('Error al actualizar el usuario');
                }
            } else {
                // Crear nuevo usuario
                $user = new User(
                    Str::uuid()->toString(),
                    trim($userData['nombre']),
                    trim($userData['apellidos']),
                    $email->getValue(),
                    $hashedPassword,
                    false // email no verificado inicialmente
                );

                // Guardar nuevo usuario
                $savedUser = $this->userRepository->save($user);
            }

            // Generar código de verificación de 6 dígitos
            // Esto invalidará códigos anteriores si el usuario ya existía
            $verificationCode = \App\Models\VerificationCode::createForUser($savedUser->getId());

            // Enviar email de verificación (usando código plano)
            $this->emailService->sendVerificationEmail($savedUser->getEmail(), $verificationCode->getPlainCode());

            $message = $existingUser && !$existingUser->isEmailVerificado() 
                ? 'Se ha enviado un nuevo código de verificación a tu email. Por favor verifica tu email.'
                : 'Usuario registrado exitosamente. Por favor verifica tu email.';

            return [
                'success' => true,
                'message' => $message,
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
