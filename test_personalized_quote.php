<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Prueba de Personalización de Frases con IA\n";
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
    
    // 2. Buscar un usuario con quiz completo
    echo "📋 Paso 2: Buscando usuario con quiz completo...\n";
    $userRepository = app(\App\Domain\Ports\UserRepositoryInterface::class);
    $userQuizResponse = \App\Models\UserQuizResponse::with('user')
        ->whereHas('user', function($query) {
            $query->where('quiz_completed', true);
        })
        ->first();
    
    if (!$userQuizResponse) {
        echo "⚠️  No se encontró ningún usuario con quiz completo.\n";
        echo "💡 Para probar completamente, necesitas:\n";
        echo "   1. Un usuario registrado y autenticado\n";
        echo "   2. Que haya completado el quiz\n\n";
        echo "📝 Continuando con prueba simulada...\n\n";
        
        // Crear datos de prueba simulados
        $testQuizData = [
            'religious_belief' => 'espirituales',
            'spiritual_practice_level' => 'moderada',
            'spiritual_practice_frequency' => 'semanalmente',
            'daily_challenges' => ['meditacion_matutina', 'practica_de_gratitud'],
            'stoic_paths' => ['paz_interior', 'autocontrol'],
            'age_range' => '25-34',
            'gender' => 'masculino',
            'country' => 'Mexico'
        ];
        
        echo "🧪 Usando datos de prueba simulados:\n";
        print_r($testQuizData);
        echo "\n";
        
        // Crear un objeto mock de UserQuizResponse para la prueba
        $mockQuiz = new \App\Models\UserQuizResponse();
        $mockQuiz->user_id = 'test-user-id';
        $mockQuiz->religious_belief = $testQuizData['religious_belief'];
        $mockQuiz->spiritual_practice_level = $testQuizData['spiritual_practice_level'];
        $mockQuiz->spiritual_practice_frequency = $testQuizData['spiritual_practice_frequency'];
        $mockQuiz->daily_challenges = $testQuizData['daily_challenges'];
        $mockQuiz->stoic_paths = $testQuizData['stoic_paths'];
        $mockQuiz->age_range = $testQuizData['age_range'];
        $mockQuiz->gender = $testQuizData['gender'];
        $mockQuiz->country = $testQuizData['country'];
        
        $userQuizResponse = $mockQuiz;
    } else {
        echo "✅ Usuario con quiz encontrado:\n";
        echo "   - User ID: {$userQuizResponse->user_id}\n";
        echo "   - Creencia religiosa: {$userQuizResponse->religious_belief}\n";
        echo "   - Nivel espiritual: {$userQuizResponse->spiritual_practice_level}\n";
        echo "   - Caminos estoicos: " . implode(', ', $userQuizResponse->stoic_paths) . "\n\n";
    }
    
    // 3. Preparar datos de la frase del día
    echo "📋 Paso 3: Preparando datos para personalización...\n";
    $dailyQuoteData = [
        'quote' => $dailyQuote->getQuote(),
        'author' => $dailyQuote->getAuthor(),
        'category' => $dailyQuote->getCategory()
    ];
    
    echo "✅ Datos preparados\n\n";
    
    // 4. Generar frase personalizada
    echo "📋 Paso 4: Generando frase personalizada con IA...\n";
    echo "⏳ Esto puede tomar unos segundos...\n\n";
    
    $startTime = microtime(true);
    $personalizeUseCase = app(\App\Application\UseCases\GeneratePersonalizedQuoteExplanation::class);
    $result = $personalizeUseCase->execute($dailyQuoteData, $userQuizResponse);
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    if (!$result['success']) {
        echo "❌ Error: " . ($result['message'] ?? 'Error desconocido') . "\n";
        exit(1);
    }
    
    echo "✅ Frase personalizada generada en {$duration} segundos\n\n";
    
    // 5. Mostrar resultados
    echo str_repeat("=", 70) . "\n";
    echo "📊 RESULTADOS DE LA PRUEBA\n";
    echo str_repeat("=", 70) . "\n\n";
    
    echo "📝 FRASE ORIGINAL DEL DÍA:\n";
    echo str_repeat("-", 70) . "\n";
    echo "\"{$dailyQuoteData['quote']}\"\n";
    echo "— {$dailyQuoteData['author']} ({$dailyQuoteData['category']})\n\n";
    
    echo "✨ FRASE PERSONALIZADA:\n";
    echo str_repeat("-", 70) . "\n";
    echo "\"{$result['data']['personalized_quote']}\"\n";
    echo "— {$result['data']['original_author']} ({$result['data']['original_category']})\n\n";
    
    echo "📖 EXPLICACIÓN PERSONALIZADA:\n";
    echo str_repeat("-", 70) . "\n";
    echo wordwrap($result['data']['explanation'], 70, "\n") . "\n\n";
    
    echo str_repeat("=", 70) . "\n";
    echo "✅ ¡Prueba completada exitosamente!\n";
    echo "✅ La personalización de frases está funcionando correctamente\n";
    echo str_repeat("=", 70) . "\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetalles:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

