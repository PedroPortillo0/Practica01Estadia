<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use Illuminate\Support\Facades\Hash;

class Password
{
    private string $value;

    public function __construct(string $plainPassword)
    {
        if (empty($plainPassword) || strlen($plainPassword) < 6) {
            throw new InvalidArgumentException('La contraseña debe tener al menos 6 caracteres');
        }
        
        $this->value = $plainPassword;
    }

    public function hash(): string
    {
        return Hash::make($this->value);
    }

    public static function verify(string $plainPassword, string $hashedPassword): bool
    {
        return Hash::check($plainPassword, $hashedPassword);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
