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

    // Propiedad temporal para almacenar el código plano
    public $plain_code = null;

    // Mutator: Codifica automáticamente en Base64 antes de guardar
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = base64_encode($value);
    }

    // Accessor: Decodifica automáticamente desde Base64 al leer
    public function getCodeAttribute($value)
    {
        return base64_decode($value);
    }

    // Método para obtener el código plano (para emails)
    public function getPlainCode(): string
    {
        return $this->plain_code ?? $this->code;
    }

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

        // Generar código plano
        $plainCode = self::generateCode();

        // Crear nuevo código (se codifica automáticamente)
        $verificationCode = self::create([
            'user_id' => $userId,
            'code' => $plainCode, // Se codificará automáticamente
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(15) // Expira en 15 minutos
        ]);

        // Asignar el código plano para enviarlo por email
        $verificationCode->plain_code = $plainCode;
        
        return $verificationCode;
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
        // Codificar el código recibido para comparar con la BD
        $encodedCode = base64_encode($code);
        
        return self::where('user_id', $userId)
                   ->whereRaw('code = ?', [$encodedCode]) // Comparación directa con código codificado
                   ->where('type', $type)
                   ->where('used', false)
                   ->where('expires_at', '>', Carbon::now())
                   ->first();
    }
}
