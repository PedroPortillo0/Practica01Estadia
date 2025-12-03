<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = $argv[1] ?? null;

if (!$userId) {
    echo "❌ Error: Debes proporcionar un User ID\n";
    echo "Uso: php check_user_quiz.php <user_id>\n";
    exit(1);
}

echo "🔍 Verificando usuario: {$userId}\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // Buscar usuario
    $user = \App\Models\User::find($userId);
    
    if (!$user) {
        echo "❌ Usuario no encontrado con ID: {$userId}\n";
        exit(1);
    }
    
    echo "✅ Usuario encontrado:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Email: {$user->email}\n";
    echo "   - Nombre: {$user->name}\n";
    echo "   - Quiz completado (campo): " . ($user->quiz_completed ? 'Sí ✅' : 'No ❌') . "\n\n";
    
    // Buscar respuesta del quiz
    $quizResponse = \App\Models\UserQuizResponse::where('user_id', $userId)->first();
    
    if ($quizResponse) {
        echo "✅ Quiz Response encontrado:\n";
        echo "   - Creencia religiosa: {$quizResponse->religious_belief}\n";
        echo "   - Nivel espiritual: {$quizResponse->spiritual_practice_level}\n";
        echo "   - Caminos estoicos: " . implode(', ', $quizResponse->stoic_paths ?? []) . "\n";
        echo "   - Desafíos diarios: " . implode(', ', $quizResponse->daily_challenges ?? []) . "\n";
        echo "   - Completado en: " . ($quizResponse->completed_at ? $quizResponse->completed_at->format('Y-m-d H:i:s') : 'N/A') . "\n\n";
    } else {
        echo "⚠️  No se encontró respuesta de quiz para este usuario\n\n";
    }
    
    // Verificar estado completo
    $hasQuizCompleted = $user->quiz_completed && $quizResponse !== null;
    
    echo str_repeat("=", 70) . "\n";
    echo "📊 RESULTADO FINAL:\n";
    echo str_repeat("=", 70) . "\n";
    
    if ($hasQuizCompleted) {
        echo "✅ El usuario SÍ tiene el quiz completo\n";
        echo "   → Recibirá frases personalizadas con IA\n";
    } else {
        echo "❌ El usuario NO tiene el quiz completo\n";
        echo "   → Recibirá frases normales del día\n";
    }
    
    echo "\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetalles:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

