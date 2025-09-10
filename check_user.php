<?php

require_once 'vendor/autoload.php';

// Cargar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = $argv[1] ?? 'fcdd01dd-f54f-4bec-81c9-736dd6281963';

$user = \App\Models\User::find($userId);

if ($user) {
    echo "📋 INFORMACIÓN DEL USUARIO:\n";
    echo "ID: {$user->id}\n";
    echo "Email: {$user->email}\n";
    echo "Email Verificado: " . ($user->email_verificado ? 'SÍ' : 'NO') . "\n";
    echo "Fecha Creación: {$user->created_at}\n";
    
    // Verificar códigos de verificación
    $codes = \App\Models\VerificationCode::where('user_id', $userId)->get();
    echo "\n🔢 CÓDIGOS DE VERIFICACIÓN:\n";
    foreach ($codes as $code) {
        echo "- Código: {$code->code}, Usado: " . ($code->used ? 'SÍ' : 'NO') . ", Expira: {$code->expires_at}\n";
    }
} else {
    echo "❌ Usuario no encontrado con ID: {$userId}\n";
}
