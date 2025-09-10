<?php

namespace App\Application\UseCases;

use App\Domain\Ports\UserRepositoryInterface;
use Exception;

class GetAllUsers
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    public function execute(int $page = 1, int $limit = 10): array
    {
        try {
            // Validar parámetros de paginación
            if ($page < 1) $page = 1;
            if ($limit < 1 || $limit > 100) $limit = 10; // Máximo 100 por página

            // Obtener usuarios con paginación
            $result = $this->userRepository->getAllWithPagination($page, $limit);

            // Formatear datos de usuarios
            $formattedUsers = array_map(function($user) {
                return $this->formatUserData($user);
            }, $result['users']);

            return [
                'success' => true,
                'message' => 'Usuarios obtenidos exitosamente',
                'data' => $formattedUsers,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $result['total'],
                    'total_pages' => ceil($result['total'] / $limit),
                    'has_next' => $page < ceil($result['total'] / $limit),
                    'has_previous' => $page > 1
                ]
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
