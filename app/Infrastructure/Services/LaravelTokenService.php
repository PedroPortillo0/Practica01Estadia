<?php

namespace App\Infrastructure\Services;

use App\Domain\Ports\TokenServiceInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class LaravelTokenService implements TokenServiceInterface
{
    private string $secret;
    private string $algorithm;
    private int $expiresIn;
    private int $verificationExpiresIn;

    public function __construct()
    {
        $this->secret = config('app.jwt_secret', config('app.key'));
        $this->algorithm = 'HS256';
        $this->expiresIn = config('app.jwt_expires_in', 86400); // 24 horas en segundos
        $this->verificationExpiresIn = config('app.verification_expires_in', 86400); // 24 horas
    }

    public function generateToken(array $payload): string
    {
        try {
            $payload['iat'] = time();
            $payload['exp'] = time() + $this->expiresIn;

            return JWT::encode($payload, $this->secret, $this->algorithm);

        } catch (Exception $e) {
            throw new Exception('Error al generar token: ' . $e->getMessage());
        }
    }

    public function verifyToken(string $token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return (array) $decoded;

        } catch (\Firebase\JWT\ExpiredException $e) {
            throw new Exception('Token expirado');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            throw new Exception('Token inválido');
        } catch (Exception $e) {
            throw new Exception('Error al verificar token: ' . $e->getMessage());
        }
    }

    public function generateVerificationToken(string $userId): string
    {
        try {
            $payload = [
                'user_id' => $userId,
                'type' => 'email_verification',
                'iat' => time(),
                'exp' => time() + $this->verificationExpiresIn
            ];

            return JWT::encode($payload, $this->secret, $this->algorithm);

        } catch (Exception $e) {
            throw new Exception('Error al generar token de verificación: ' . $e->getMessage());
        }
    }
}
