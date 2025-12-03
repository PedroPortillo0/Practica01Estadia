<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Iniciando prueba del motor de IA...\n\n";

try {
    // Verificar configuración
    echo "📋 Verificando configuración...\n";
    $provider = config('ai.provider');
    $apiKey = config('ai.api_key');
    $baseUrl = config('ai.base_url');
    
    echo "   Provider: " . ($provider ?: 'NO CONFIGURADO') . "\n";
    echo "   API Key: " . ($apiKey ? substr($apiKey, 0, 10) . '...' : 'NO CONFIGURADO') . "\n";
    echo "   Base URL: " . ($baseUrl ?: 'NO CONFIGURADO') . "\n\n";
    
    if (empty($apiKey) || empty($baseUrl)) {
        echo "❌ Error: Configuración incompleta. Verifica tu archivo .env\n";
        exit(1);
    }
    
    // Obtener el servicio de IA
    echo "🔌 Obteniendo servicio de IA...\n";
    $aiService = app(\App\Domain\Ports\AIServiceInterface::class);
    echo "✅ Servicio obtenido correctamente\n\n";
    
    // Crear prompt de prueba
    echo "📝 Generando 3 frases de prueba...\n";
    $prompt = "Genera exactamente 3 frases estoicas diarias únicas. 

Formato JSON:
{
  \"quotes\": [
    {
      \"quote\": \"Texto de la frase\",
      \"author\": \"Marco Aurelio\",
      \"category\": \"Sabiduría\"
    }
  ]
}

Responde SOLO con el JSON, sin texto adicional.";
    
    echo "⏳ Enviando petición a la API de IA...\n";
    $startTime = microtime(true);
    
    $generatedText = $aiService->generateText($prompt, [
        'temperature' => 0.9,
        'max_tokens' => 2000,
    ]);
    
    $endTime = microtime(true);
    $duration = round($endTime - $startTime, 2);
    
    echo "✅ Respuesta recibida en {$duration} segundos\n\n";
    echo "📋 Contenido generado:\n";
    echo str_repeat("─", 70) . "\n";
    echo $generatedText . "\n";
    echo str_repeat("─", 70) . "\n\n";
    
    // Intentar parsear JSON
    $jsonMatch = [];
    if (preg_match('/\{[\s\S]*\}/', $generatedText, $jsonMatch)) {
        $json = json_decode($jsonMatch[0], true);
        
        if (isset($json['quotes']) && is_array($json['quotes'])) {
            echo "✅ JSON parseado correctamente\n";
            echo "✅ Se generaron " . count($json['quotes']) . " frases\n\n";
            
            echo "📊 Frases generadas:\n";
            foreach ($json['quotes'] as $index => $quote) {
                echo "\n" . ($index + 1) . ". " . ($quote['quote'] ?? 'N/A') . "\n";
                echo "   Autor: " . ($quote['author'] ?? 'N/A') . "\n";
                echo "   Categoría: " . ($quote['category'] ?? 'N/A') . "\n";
            }
            
            echo "\n\n✅ ¡Prueba completada exitosamente!\n";
            echo "✅ El motor de IA está funcionando correctamente\n";
            exit(0);
        } else {
            echo "⚠️  El JSON no contiene el formato esperado\n";
        }
    } else {
        echo "⚠️  No se pudo encontrar JSON en la respuesta\n";
    }
    
    echo "\nℹ️  La conexión con la IA funciona, pero el formato puede necesitar ajustes.\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "\nDetalles:\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

