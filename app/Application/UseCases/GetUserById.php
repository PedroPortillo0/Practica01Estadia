<?php

namespace App\Application\UseCases;

use App\Domain\Ports\UserRepositoryInterface;
use Exception;

class GetUserById
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(string $userId): array
    {
        try {
            if (empty($userId)) {
                throw new Exception('ID de usuario es requerido');
            }

            // Buscar usuario por ID
            $user = $this->userRepository->findById($userId);
            
            if (!$user) {
                throw new Exception('Usuario no encontrado');
            }

            return [
                'success' => true,
                'message' => 'Usuario encontrado exitosamente',
                'data' => $this->formatUserData($user)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function formatUserData($user): array
    {
        return [
            'id' => $user->getId(),
            'nombre' => $user->getNombre(),
            'apellidoPaterno' => $user->getApellidoPaterno(),
            'apellidoMaterno' => $user->getApellidoMaterno(),
            'nombreCompleto' => $user->getNombreCompleto(),
            'telefono' => $user->getTelefono(),
            'email' => $user->getEmail(),
            'emailVerificado' => $user->isEmailVerificado(),
            'fechaCreacion' => $user->getFechaCreacion()->format('Y-m-d H:i:s')
        ];
    }
}
