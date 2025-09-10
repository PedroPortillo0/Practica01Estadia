<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VerificationCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'type',
        'used',
        'expires_at'
    ];

    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime'
    ];

    // Generar código de 6 dígitos
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // Crear nuevo código de verificación
    public static function createForUser(string $userId, string $type = 'email_verification'): self
    {
        // Invalidar códigos anteriores del mismo tipo
        self::where('user_id', $userId)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        // Crear nuevo código
        return self::create([
            'user_id' => $userId,
            'code' => self::generateCode(),
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(15) // Expira en 15 minutos
        ]);
    }

    // Verificar si el código es válido
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at > Carbon::now();
    }

    // Marcar como usado
    public function markAsUsed(): void
    {
        $this->update(['used' => true]);
    }

    // Buscar código válido
    public static function findValidCode(string $userId, string $code, string $type = 'email_verification'): ?self
    {
        return self::where('user_id', $userId)
                   ->where('code', $code)
                   ->where('type', $type)
                   ->where('used', false)
                   ->where('expires_at', '>', Carbon::now())
                   ->first();
    }
}
