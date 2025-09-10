<?php

require_once 'vendor/autoload.php';

// Cargar Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $emailService = app(\App\Domain\Ports\EmailServiceInterface::class);
    $tokenService = app(\App\Domain\Ports\TokenServiceInterface::class);
    
    // Generar token de prueba
    $token = $tokenService->generateVerificationToken('test-user-id');
    
    // Enviar email de prueba
    $emailService->sendVerificationEmail('pedro.portillo.r22@gmail.com', $token);
    
    echo "✅ ¡Email de prueba enviado correctamente!\n";
    echo "📧 Revisa tu bandeja de entrada: pedro.portillo.r22@gmail.com\n";
    echo "🔗 Token: $token\n";
    
} catch (Exception $e) {
    echo "❌ Error al enviar email: " . $e->getMessage() . "\n";
    echo "💡 Verifica tu configuración de Gmail en .env\n";
}
