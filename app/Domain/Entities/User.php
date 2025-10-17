<?php

namespace App\Domain\Entities;

use DateTime;

class User
{
    public function __construct(
        private string $id,
        private string $nombre,
        private string $apellidos,
        private string $email,
        private ?string $password,
        private bool $emailVerificado = false,
        private ?DateTime $fechaCreacion = null,
        private ?string $googleId = null,
        private ?string $avatar = null,
        private string $authProvider = 'local'
    ) {
        $this->fechaCreacion = $fechaCreacion ?? new DateTime();
    }

    // Getters
    public function getId(): string
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function isEmailVerificado(): bool
    {
        return $this->emailVerificado;
    }

    public function getFechaCreacion(): DateTime
    {
        return $this->fechaCreacion;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function getAuthProvider(): string
    {
        return $this->authProvider;
    }

    // Métodos de negocio
    public function getNombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function verificarEmail(): void
    {
        $this->emailVerificado = true;
    }

    // Validaciones de dominio
    public static function validarDatos(array $userData): array
    {
        $errores = [];

        if (empty($userData['nombre']) || strlen(trim($userData['nombre'])) < 2) {
            $errores[] = 'El nombre debe tener al menos 2 caracteres';
        }

        if (empty($userData['apellidos']) || strlen(trim($userData['apellidos'])) < 2) {
            $errores[] = 'Los apellidos deben tener al menos 2 caracteres';
        }

        if (empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email debe tener un formato válido';
        }

        // Solo validar password si no es un registro con OAuth
        $authProvider = $userData['auth_provider'] ?? 'local';
        if ($authProvider === 'local') {
            if (empty($userData['password']) || strlen($userData['password']) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres';
            }

            if (empty($userData['confirm_password']) || $userData['password'] !== $userData['confirm_password']) {
                $errores[] = 'Las contraseñas no coinciden';
            }
        }

        return $errores;
    }

    // Método para crear array serializable
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'email' => $this->email,
            'emailVerificado' => $this->emailVerificado,
            'googleId' => $this->googleId,
            'avatar' => $this->avatar,
            'authProvider' => $this->authProvider,
            'fechaCreacion' => $this->fechaCreacion->format('Y-m-d H:i:s')
        ];
    }
}
