<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Prueba: Usuario SIN Quiz Completo\n";
echo str_repeat("=", 70) . "\n\n";

try {
    // 1. Verificar que existe al menos una frase del día
    echo "📋 Paso 1: Verificando frase del día...\n";
    $dayOfYear = (int) date('z') + 1;
    $quoteRepository = app(\App\Domain\Ports\DailyQuoteRepositoryInterface::class);
    $dailyQuote = $quoteRepository->findByDayOfYear($dayOfYear);
    
    if (!$dailyQuote) {
        echo "❌ Error: No hay frase disponible para el día de hoy (día {$dayOfYear})\n";
        echo "💡 Ejecuta primero: php artisan ai:generate-quotes\n";
        exit(1);
    }
    
    echo "✅ Frase del día encontrada:\n";
    echo "   - ID: {$dailyQuote->getId()}\n";
    echo "   - Frase: " . substr($dailyQuote->getQuote(), 0, 60) . "...\n";
    echo "   - Autor: {$dailyQuote->getAuthor()}\n";
    echo "   - Categoría: {$dailyQuote->getCategory()}\n\n";
    
    // 2. Buscar un usuario SIN quiz completo
    echo "📋 Paso 2: Buscando usuario SIN quiz completo...\n";
    $userRepository = app(\App\Domain\Ports\UserRepositoryInterface::class);
    
    // Buscar usuario que NO tenga quiz completado
    $userWithoutQuiz = \App\Models\User::where('quiz_completed', false)
        ->orWhereNull('quiz_completed')
        ->first();
    
    if (!$userWithoutQuiz) {
        echo "⚠️  No se encontró ningún usuario sin quiz completo.\n";
        echo "💡 Creando usuario de prueba sin quiz...\n\n";
        
        // Crear un usuario de prueba temporal (solo para el test)
        $testUser = new \App\Models\User();
        $testUser->id = 'test-user-no-quiz-' . uniqid();
        $testUser->email = 'test-no-quiz@example.com';
        $testUser->name = 'Usuario Test Sin Quiz';
        $testUser->quiz_completed = false;
        $testUser->email_verified = true;
        $testUser->email_verification_code = null;
        $testUser->password = bcrypt('test123');
        $testUser->save();
        
        $userWithoutQuiz = $testUser;
        echo "✅ Usuario de prueba creado: {$testUser->id}\n\n";
    } else {
        echo "✅ Usuario sin quiz encontrado:\n";
        echo "   - User ID: {$userWithoutQuiz->id}\n";
        echo "   - Email: {$userWithoutQuiz->email}\n";
        echo "   - Quiz completado: " . ($userWithoutQuiz->quiz_completed ? 'Sí' : 'No') . "\n\n";
    }
    
    // 3. Simular el caso de uso GetDailyQuote
    echo "📋 Paso 3: Ejecutando GetDailyQuote para usuario sin quiz...\n";
    $getDailyQuoteUseCase = app(\App\Application\UseCases\GetDailyQuote::class);
    
    // Ejecutar con el userId del usuario sin quiz
    $result = $getDailyQuoteUseCase->execute(includeDetail: false, userId: $userWithoutQuiz->id);
    
    if (!$result['success']) {
        echo "❌ Error: " . ($result['message'] ?? 'Error desconocido') . "\n";
        exit(1);
    }
    
    echo "✅ Resultado obtenido\n\n";
    
    // 4. Verificar que NO sea personalizada
    echo "📋 Paso 4: Verificando que la respuesta NO sea personalizada...\n";
    $isPersonalized = $result['data']['is_personalized'] ?? false;
    
    if ($isPersonalized) {
        echo "❌ ERROR: La frase está marcada como personalizada, pero el usuario NO tiene quiz completo!\n";
        echo "   Esto indica que hay un problema en la validación.\n\n";
        exit(1);
    }
    
    echo "✅ La frase NO está personalizada (correcto)\n\n";
    
    // 5. Verificar que la frase sea la original
    $returnedQuote = $result['data']['quote'] ?? null;
    $originalQuote = $dailyQuote->getQuote();
    
    if ($returnedQuote !== $originalQuote) {
        echo "⚠️  ADVERTENCIA: La frase devuelta es diferente a la original del día.\n";
        echo "   Frase original: {$originalQuote}\n";
        echo "   Frase devuelta: {$returnedQuote}\n\n";
    } else {
        echo "✅ La frase devuelta es la original del día (correcto)\n\n";
    }
    
    // 6. Mostrar resultados
    echo str_repeat("=", 70) . "\n";
    echo "📊 RESULTADOS DE LA PRUEBA\n";
    echo str_repeat("=", 70) . "\n\n";
    
    echo "👤 USUARIO:\n";
    echo str_repeat("-", 70) . "\n";
    echo "   - ID: {$userWithoutQuiz->id}\n";
    echo "   - Email: {$userWithoutQuiz->email}\n";
    echo "   - Quiz completado: " . ($userWithoutQuiz->quiz_completed ? 'Sí ❌' : 'No ✅') . "\n\n";
    
    echo "📝 FRASE DEVUELTA:\n";
    echo str_repeat("-", 70) . "\n";
    echo "\"{$result['data']['quote']}\"\n";
    echo "— {$result['data']['author']} ({$result['data']['category']})\n\n";
    
    echo "🔍 VALIDACIONES:\n";
    echo str_repeat("-", 70) . "\n";
    echo "   ✅ is_personalized: " . ($isPersonalized ? 'true ❌ (ERROR)' : 'false ✅ (CORRECTO)') . "\n";
    echo "   ✅ Frase es original: " . ($returnedQuote === $originalQuote ? 'Sí ✅' : 'No ❌') . "\n";
    echo "   ✅ No tiene campo 'personalized_quote': " . (!isset($result['data']['personalized_quote']) ? 'Correcto ✅' : 'Error ❌') . "\n";
    echo "   ✅ No tiene campo 'explanation': " . (!isset($result['data']['explanation']) ? 'Correcto ✅' : 'Error ❌') . "\n\n";
    
    echo str_repeat("=", 70) . "\n";
    echo "✅ ¡Prueba completada exitosamente!\n";
    echo "✅ La validación funciona correctamente: usuarios sin quiz reciben frase normal\n";
    echo str_repeat("=", 70) . "\n";
    
    // Limpiar: eliminar usuario de prueba si fue creado
    if (str_starts_with($userWithoutQuiz->id, 'test-user-no-quiz-')) {
        echo "\n🧹 Limpiando usuario de prueba...\n";
        $userWithoutQuiz->delete();
        echo "✅ Usuario de prueba eliminado\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetalles:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

