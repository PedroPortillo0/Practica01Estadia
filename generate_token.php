<?php

require_once 'vendor/autoload.php';

// Cargar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Obtener el usuario por email
$email = $argv[1] ?? 'pedro.portillo.r22@gmail.com';
$user = \App\Models\User::where('email', $email)->first();

if (!$user) {
    echo "❌ Usuario con email '$email' no encontrado.\n";
    echo "📋 Usuarios disponibles:\n";
    \App\Models\User::all()->each(function($u) {
        echo "   - {$u->email} (ID: {$u->id})\n";
    });
    exit(1);
}

// Generar token de verificación
$tokenService = app(\App\Domain\Ports\TokenServiceInterface::class);
$token = $tokenService->generateVerificationToken($user->id);

echo "✅ Token de verificación generado para: {$user->email}\n";
echo "🔗 URL completa:\n";
echo "GET http://localhost:8000/api/users/verify-email?token={$token}\n\n";
echo "📋 Información del usuario:\n";
echo "   - ID: {$user->id}\n";
echo "   - Nombre: {$user->nombre} {$user->apellido_paterno}\n";
echo "   - Email verificado: " . ($user->email_verificado ? 'SÍ' : 'NO') . "\n";
