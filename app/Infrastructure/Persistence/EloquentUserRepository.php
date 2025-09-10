<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\User;
use App\Domain\Ports\UserRepositoryInterface;
use App\Models\User as UserModel;
use DateTime;
use Exception;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        try {
            $userModel = UserModel::create([
                'id' => $user->getId(),
                'nombre' => $user->getNombre(),
                'apellido_paterno' => $user->getApellidoPaterno(),
                'apellido_materno' => $user->getApellidoMaterno(),
                'telefono' => $user->getTelefono(),
                'email' => $user->getEmail(),
                'password' => $user->getPassword(),
                'email_verificado' => $user->isEmailVerificado(),
                'created_at' => $user->getFechaCreacion()
            ]);

            return $this->toDomainEntity($userModel);

        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                throw new Exception('El email ya está registrado');
            }
            throw new Exception('Error al guardar usuario: ' . $e->getMessage());
        }
    }

    public function findByEmail(string $email): ?User
    {
        try {
            $userModel = UserModel::where('email', $email)->first();
            return $userModel ? $this->toDomainEntity($userModel) : null;

        } catch (Exception $e) {
            throw new Exception('Error al buscar usuario por email: ' . $e->getMessage());
        }
    }

    public function findById(string $id): ?User
    {
        try {
            $userModel = UserModel::where('id', $id)->first();
            return $userModel ? $this->toDomainEntity($userModel) : null;

        } catch (Exception $e) {
            throw new Exception('Error al buscar usuario por ID: ' . $e->getMessage());
        }
    }

    public function update(string $id, array $userData): ?User
    {
        try {
            $userModel = UserModel::where('id', $id)->first();
            
            if (!$userModel) {
                return null;
            }

            $userModel->update($userData);
            return $this->toDomainEntity($userModel);

        } catch (Exception $e) {
            throw new Exception('Error al actualizar usuario: ' . $e->getMessage());
        }
    }

    public function delete(string $id): bool
    {
        try {
            return UserModel::where('id', $id)->delete() > 0;

        } catch (Exception $e) {
            throw new Exception('Error al eliminar usuario: ' . $e->getMessage());
        }
    }

    public function exists(string $email): bool
    {
        try {
            return UserModel::where('email', $email)->exists();

        } catch (Exception $e) {
            throw new Exception('Error al verificar existencia del usuario: ' . $e->getMessage());
        }
    }

    private function toDomainEntity(UserModel $userModel): User
    {
        return new User(
            $userModel->id,
            $userModel->nombre,
            $userModel->apellido_paterno,
            $userModel->apellido_materno,
            $userModel->telefono,
            $userModel->email,
            $userModel->password,
            $userModel->email_verificado,
            new DateTime($userModel->created_at)
        );
    }

    public function getAllWithPagination(int $page, int $limit): array
    {
        try {
            $offset = ($page - 1) * $limit;
            
            // Obtener total de usuarios
            $total = UserModel::count();
            
            // Obtener usuarios con paginación
            $userModels = UserModel::orderBy('created_at', 'desc')
                                  ->offset($offset)
                                  ->limit($limit)
                                  ->get();
            
            // Convertir a entidades de dominio
            $users = $userModels->map(function($userModel) {
                return $this->toDomainEntity($userModel);
            })->toArray();

            return [
                'users' => $users,
                'total' => $total
            ];

        } catch (Exception $e) {
            throw new Exception('Error al obtener usuarios: ' . $e->getMessage());
        }
    }
}
