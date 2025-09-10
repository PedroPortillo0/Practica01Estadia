<?php

namespace App\Domain\Ports;

interface TokenServiceInterface
{
    public function generateToken(array $payload): string;
    public function verifyToken(string $token): array;
    public function generateVerificationToken(string $userId): string;
}
