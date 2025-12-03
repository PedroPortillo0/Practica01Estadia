<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$userId = $argv[1] ?? '34c6aded-a26b-49ba-b493-39cc3010680f';

echo "🧪 Prueba de Personalización vía API\n";
echo str_repeat("=", 70) . "\n\n";

try {
    echo "📋 Verificando usuario: {$userId}\n";
    
    // Verificar usuario
    $userRepository = app(\App\Domain\Ports\UserRepositoryInterface::class);
    $user = $userRepository->findById($userId);
    
    if (!$user) {
        echo "❌ Usuario no encontrado\n";
        exit(1);
    }
    
    echo "✅ Usuario encontrado: {$user->getEmail()}\n";
    echo "   - Quiz completado: " . ($user->isQuizCompleted() ? 'Sí ✅' : 'No ❌') . "\n\n";
    
    // Verificar quiz response
    $userQuiz = \App\Models\UserQuizResponse::where('user_id', $userId)->first();
    if ($userQuiz) {
        echo "✅ Quiz response encontrado\n\n";
    } else {
        echo "❌ No se encontró quiz response\n\n";
    }
    
    // Simular el caso de uso
    echo "📋 Ejecutando GetDailyQuote...\n";
    $getDailyQuote = app(\App\Application\UseCases\GetDailyQuote::class);
    $result = $getDailyQuote->execute(includeDetail: false, userId: $userId);
    
    echo "✅ Resultado obtenido\n\n";
    
    // Mostrar resultado
    echo str_repeat("=", 70) . "\n";
    echo "📊 RESULTADO:\n";
    echo str_repeat("=", 70) . "\n";
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
    
    // Verificar si es personalizada
    if (isset($result['data']['is_personalized']) && $result['data']['is_personalized']) {
        echo "✅ FRASE PERSONALIZADA generada correctamente\n";
        echo "   - Frase: " . substr($result['data']['personalized_quote'] ?? 'N/A', 0, 60) . "...\n";
    } else {
        echo "⚠️  FRASE NORMAL (no personalizada)\n";
        echo "   - Frase: " . substr($result['data']['quote'] ?? 'N/A', 0, 60) . "...\n";
        echo "\n   Posibles causas:\n";
        echo "   - Usuario no tiene quiz completo\n";
        echo "   - Error al generar personalización (revisa logs)\n";
        echo "   - Dependencias no inyectadas correctamente\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetalles:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

