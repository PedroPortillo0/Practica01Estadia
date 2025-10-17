<?php

/**
 * Script de prueba para verificar la configuración de Google OAuth
 * 
 * Ejecutar: php test_google_oauth.php
 */

require __DIR__.'/vendor/autoload.php';

use Illuminate\Foundation\Application;

echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║       Test de Configuración - Google OAuth                  ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

// Cargar aplicación Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "✓ Aplicación Laravel cargada\n\n";

// Verificar instalación de Socialite
echo "📦 Verificando Laravel Socialite...\n";
if (class_exists('Laravel\Socialite\Facades\Socialite')) {
    echo "   ✓ Laravel Socialite está instalado\n\n";
} else {
    echo "   ✗ Laravel Socialite NO está instalado\n";
    echo "   Ejecuta: composer require laravel/socialite\n\n";
    exit(1);
}

// Verificar variables de entorno
echo "🔑 Verificando variables de entorno...\n";

$clientId = env('GOOGLE_CLIENT_ID');
$clientSecret = env('GOOGLE_CLIENT_SECRET');
$redirectUri = env('GOOGLE_REDIRECT_URI');

if ($clientId && $clientId !== 'your-client-id.apps.googleusercontent.com') {
    echo "   ✓ GOOGLE_CLIENT_ID configurado\n";
} else {
    echo "   ✗ GOOGLE_CLIENT_ID no configurado o usa valor de ejemplo\n";
}

if ($clientSecret && $clientSecret !== 'your-client-secret') {
    echo "   ✓ GOOGLE_CLIENT_SECRET configurado\n";
} else {
    echo "   ✗ GOOGLE_CLIENT_SECRET no configurado o usa valor de ejemplo\n";
}

if ($redirectUri) {
    echo "   ✓ GOOGLE_REDIRECT_URI: {$redirectUri}\n";
} else {
    echo "   ✗ GOOGLE_REDIRECT_URI no configurado\n";
}

echo "\n";

// Verificar archivos clave
echo "📁 Verificando archivos clave...\n";

$files = [
    'app/Application/UseCases/LoginWithGoogle.php' => 'Caso de uso LoginWithGoogle',
    'app/Http/Controllers/GoogleAuthController.php' => 'Controlador GoogleAuth',
    'database/migrations/2025_10_15_154700_add_oauth_fields_to_users_table.php' => 'Migración OAuth',
];

foreach ($files as $file => $description) {
    if (file_exists(__DIR__.'/'.$file)) {
        echo "   ✓ {$description}\n";
    } else {
        echo "   ✗ {$description} NO ENCONTRADO\n";
    }
}

echo "\n";

// Verificar configuración de servicios
echo "⚙️  Verificando configuración de servicios...\n";

try {
    $googleConfig = config('services.google');
    
    if ($googleConfig) {
        echo "   ✓ Configuración de Google en services.php\n";
        echo "     - Client ID: " . ($googleConfig['client_id'] ? '✓' : '✗') . "\n";
        echo "     - Client Secret: " . ($googleConfig['client_secret'] ? '✓' : '✗') . "\n";
        echo "     - Redirect URI: " . ($googleConfig['redirect'] ? '✓' : '✗') . "\n";
    } else {
        echo "   ✗ Configuración de Google no encontrada en services.php\n";
    }
} catch (Exception $e) {
    echo "   ✗ Error al leer configuración: {$e->getMessage()}\n";
}

echo "\n";

// Verificar rutas
echo "🛣️  Verificando rutas registradas...\n";

try {
    $routes = app('router')->getRoutes();
    
    $requiredRoutes = [
        'api/auth/google/redirect',
        'api/auth/google/callback',
        'api/auth/google/token',
    ];
    
    foreach ($requiredRoutes as $route) {
        $found = false;
        foreach ($routes as $r) {
            if (str_contains($r->uri(), $route)) {
                $found = true;
                break;
            }
        }
        
        if ($found) {
            echo "   ✓ {$route}\n";
        } else {
            echo "   ✗ {$route} NO ENCONTRADA\n";
        }
    }
} catch (Exception $e) {
    echo "   ⚠ No se pudieron verificar las rutas: {$e->getMessage()}\n";
}

echo "\n";

// Resumen
echo "╔══════════════════════════════════════════════════════════════╗\n";
echo "║                      RESUMEN                                 ║\n";
echo "╚══════════════════════════════════════════════════════════════╝\n\n";

if (!$clientId || !$clientSecret || !$redirectUri || 
    $clientId === 'your-client-id.apps.googleusercontent.com' ||
    $clientSecret === 'your-client-secret') {
    echo "⚠️  ACCIÓN REQUERIDA:\n\n";
    echo "1. Ve a: https://console.cloud.google.com/\n";
    echo "2. Crea credenciales OAuth 2.0\n";
    echo "3. Agrega estas líneas a tu archivo .env:\n\n";
    echo "   GOOGLE_CLIENT_ID=tu-client-id.apps.googleusercontent.com\n";
    echo "   GOOGLE_CLIENT_SECRET=tu-client-secret\n";
    echo "   GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback\n\n";
    echo "4. Ejecuta: php artisan config:clear\n";
    echo "5. Ejecuta: php artisan migrate\n\n";
} else {
    echo "✅ ¡Configuración completa!\n\n";
    echo "Puedes probar con:\n";
    echo "   php artisan serve\n\n";
    echo "Luego visita:\n";
    echo "   http://localhost:8000/api/auth/google/redirect\n\n";
}

echo "📖 Para más información, consulta: GOOGLE_AUTH_SETUP.md\n";
echo "🚀 Para inicio rápido, consulta: INICIO_RAPIDO_GOOGLE.md\n\n";




