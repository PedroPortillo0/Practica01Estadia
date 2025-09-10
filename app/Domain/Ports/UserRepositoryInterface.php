<?php

namespace App\Domain\Ports;

use App\Domain\Entities\User;

interface UserRepositoryInterface
{
    public function save(User $user): User;
    public function findByEmail(string $email): ?User;
    public function findById(string $id): ?User;
    public function update(string $id, array $userData): ?User;
    public function delete(string $id): bool;
    public function exists(string $email): bool;
    public function getAllWithPagination(int $page, int $limit): array;
}
