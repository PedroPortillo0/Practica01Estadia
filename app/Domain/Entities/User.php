<?php

namespace App\Domain\Entities;

use DateTime;

class User
{
    public function __construct(
        private string $id,
        private string $nombre,
        private string $apellidoPaterno,
        private string $apellidoMaterno,
        private string $telefono,
        private string $email,
        private string $password,
        private bool $emailVerificado = false,
        private ?DateTime $fechaCreacion = null
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

    public function getApellidoPaterno(): string
    {
        return $this->apellidoPaterno;
    }

    public function getApellidoMaterno(): string
    {
        return $this->apellidoMaterno;
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
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

    // Métodos de negocio
    public function getNombreCompleto(): string
    {
        return "{$this->nombre} {$this->apellidoPaterno} {$this->apellidoMaterno}";
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

        if (empty($userData['apellidoPaterno']) || strlen(trim($userData['apellidoPaterno'])) < 2) {
            $errores[] = 'El apellido paterno debe tener al menos 2 caracteres';
        }

        if (empty($userData['apellidoMaterno']) || strlen(trim($userData['apellidoMaterno'])) < 2) {
            $errores[] = 'El apellido materno debe tener al menos 2 caracteres';
        }

        if (empty($userData['telefono']) || !preg_match('/^\d{10}$/', $userData['telefono'])) {
            $errores[] = 'El teléfono debe tener exactamente 10 dígitos';
        }

        if (empty($userData['email']) || !filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El email debe tener un formato válido';
        }

        if (empty($userData['password']) || strlen($userData['password']) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        return $errores;
    }

    // Método para crear array serializable
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellidoPaterno' => $this->apellidoPaterno,
            'apellidoMaterno' => $this->apellidoMaterno,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'emailVerificado' => $this->emailVerificado,
            'fechaCreacion' => $this->fechaCreacion->format('Y-m-d H:i:s')
        ];
    }
}
