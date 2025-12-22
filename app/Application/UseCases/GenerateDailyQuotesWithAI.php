<?php

namespace App\Application\UseCases;

use App\Domain\Entities\DailyQuote;
use App\Domain\Ports\AIServiceInterface;
use App\Domain\Ports\DailyQuoteRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class GenerateDailyQuotesWithAI
{
    private AIServiceInterface $aiService;
    private DailyQuoteRepositoryInterface $quoteRepository;
    private int $batchSize = 10; // Generar 10 frases por lote

    public function __construct(
        AIServiceInterface $aiService,
        DailyQuoteRepositoryInterface $quoteRepository
    ) {
        $this->aiService = $aiService;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Genera 365 o 366 frases diarias usando IA y las guarda en la base de datos
     * 
     * @param int|null $year Año para determinar si es bisiesto (366 días) o no (365 días)
     * @param int $batchSize Tamaño del lote para generar frases (por defecto 10)
     * @return array Resultado de la operación
     */
    public function execute(?int $year = null, int $batchSize = 10): array
    {
        try {
            // Determinar si el año es bisiesto
            if ($year === null) {
                $year = (int) date('Y');
            }
            
            $isLeapYear = $this->isLeapYear($year);
            $totalDays = $isLeapYear ? 366 : 365;
            $this->batchSize = $batchSize;

            Log::info("Iniciando generación de {$totalDays} frases diarias para el año {$year} en lotes de {$batchSize}");

            $saved = 0;
            $errors = 0;
            $skipped = 0;

            // Generar y guardar en lotes
            for ($startDay = 1; $startDay <= $totalDays; $startDay += $this->batchSize) {
                $endDay = min($startDay + $this->batchSize - 1, $totalDays);
                $batchDays = $endDay - $startDay + 1;

                Log::info("Generando lote: días {$startDay} a {$endDay} ({$batchDays} frases)");

                try {
                    // Generar frases para este lote
                    $quotes = $this->generateQuotesBatch($batchDays, $startDay);
                    
                    Log::info("Lote generado exitosamente. Total de frases: " . count($quotes));
                    
                    if (empty($quotes)) {
                        Log::error("⚠️ El lote está vacío. No se generaron frases para los días {$startDay}-{$endDay}");
                        $errors += $batchDays;
                        continue;
                    }

                    // Guardar las frases generadas
                    foreach ($quotes as $index => $quoteData) {
                        try {
                            $dayOfYear = $startDay + $index;

                            // Verificar si ya existe una frase para este día
                            $existing = $this->quoteRepository->findByDayOfYear($dayOfYear);
                            if ($existing) {
                                Log::warning("Ya existe una frase para el día {$dayOfYear}, se omitirá");
                                $skipped++;
                                continue;
                            }

                            // Crear entidad de dominio
                            $quote = new DailyQuote(
                                $quoteData['quote'],
                                $quoteData['author'],
                                $quoteData['category'],
                                $dayOfYear,
                                true // is_active
                            );

                            Log::info("Guardando frase para el día {$dayOfYear}: " . substr($quoteData['quote'], 0, 50) . "...");
                            
                            // Guardar
                            $savedQuote = $this->quoteRepository->save($quote);
                            $saved++;

                            Log::info("✅ Frase guardada exitosamente para el día {$dayOfYear} (ID: " . $savedQuote->getId() . ")");

                        } catch (Exception $e) {
                            $errors++;
                            Log::error("Error guardando frase para el día " . ($startDay + $index) . ": " . $e->getMessage());
                        }
                    }

                    // Delay entre lotes para evitar rate limiting (excepto en el último lote)
                    if ($endDay < $totalDays) {
                        $delaySeconds = 5; // Aumentado a 5 segundos para evitar cuota
                        Log::info("Esperando {$delaySeconds} segundos antes del siguiente lote...");
                        sleep($delaySeconds);
                    }

                } catch (Exception $e) {
                    $errorMessage = $e->getMessage();
                    Log::error("Error generando lote de días {$startDay}-{$endDay}: " . $errorMessage);
                    
                    // Si es un error de cuota, extraer el tiempo de espera recomendado y reintentar
                    if (strpos($errorMessage, '429') !== false || strpos($errorMessage, 'quota') !== false || strpos($errorMessage, 'RESOURCE_EXHAUSTED') !== false) {
                        // Verificar si la cuota está completamente agotada (limit: 0)
                        $quotaExhausted = strpos($errorMessage, 'limit: 0') !== false;
                        
                        if ($quotaExhausted) {
                            Log::error("⚠️ CUOTA COMPLETAMENTE AGOTADA: La cuota gratuita de Gemini está agotada. Límite: 0");
                            Log::error("💡 Soluciones:");
                            Log::error("   1. Esperar a que se resetee la cuota (puede ser diaria o mensual)");
                            Log::error("   2. Usar una API key diferente");
                            Log::error("   3. Actualizar a un plan de pago en Google AI Studio");
                            Log::error("   4. Revisar: https://ai.google.dev/gemini-api/docs/rate-limits");
                            
                            // Si la cuota está completamente agotada, esperar más tiempo antes de continuar
                            $waitTime = 300; // 5 minutos
                            Log::warning("Esperando {$waitTime} segundos antes de continuar (cuota agotada)...");
                            sleep($waitTime);
                        } else {
                            $waitTime = $this->extractRetryDelay($errorMessage);
                            Log::warning("Error de cuota detectado. Esperando {$waitTime} segundos antes de reintentar...");
                            sleep($waitTime);
                        }
                        
                        // Reintentar el lote una vez más
                        try {
                            Log::info("Reintentando generación del lote: días {$startDay} a {$endDay}");
                            $quotes = $this->generateQuotesBatch($batchDays, $startDay);
                            
                            if (!empty($quotes)) {
                                // Guardar las frases del reintento
                                foreach ($quotes as $index => $quoteData) {
                                    try {
                                        $dayOfYear = $startDay + $index;
                                        $existing = $this->quoteRepository->findByDayOfYear($dayOfYear);
                                        if ($existing) {
                                            $skipped++;
                                            continue;
                                        }
                                        
                                        $quote = new DailyQuote(
                                            $quoteData['quote'],
                                            $quoteData['author'],
                                            $quoteData['category'],
                                            $dayOfYear,
                                            true
                                        );
                                        
                                        $this->quoteRepository->save($quote);
                                        $saved++;
                                        Log::info("✅ Frase guardada exitosamente para el día {$dayOfYear}");
                                    } catch (Exception $saveError) {
                                        $errors++;
                                        Log::error("Error guardando frase para el día " . ($startDay + $index) . ": " . $saveError->getMessage());
                                    }
                                }
                                
                                // Continuar con el siguiente lote
                                if ($endDay < $totalDays) {
                                    $delaySeconds = 5; // Aumentar delay después de un error de cuota
                                    Log::info("Esperando {$delaySeconds} segundos antes del siguiente lote...");
                                    sleep($delaySeconds);
                                }
                                continue; // Continuar con el siguiente lote
                            }
                        } catch (Exception $retryError) {
                            Log::error("Error en reintento del lote: " . $retryError->getMessage());
                        }
                    }
                    
                    // Si no es error de cuota o el reintento falló, marcar como error
                    $errors += $batchDays;
                }
            }

            return [
                'success' => true,
                'message' => "Generación completada: {$saved} frases guardadas, {$errors} errores, {$skipped} omitidas",
                'total_days' => $totalDays,
                'saved' => $saved,
                'errors' => $errors,
                'skipped' => $skipped,
                'year' => $year,
                'is_leap_year' => $isLeapYear
            ];

        } catch (Exception $e) {
            Log::error('Error generando frases con IA: ' . $e->getMessage());
            throw new Exception('Error al generar frases diarias: ' . $e->getMessage());
        }
    }

    /**
     * Genera un lote de frases usando IA
     */
    private function generateQuotesBatch(int $count, int $startDay): array
    {
        $prompt = $this->buildPrompt($count, $startDay);

        Log::info("Generando {$count} frases con IA (días {$startDay} a " . ($startDay + $count - 1) . ")...");

        try {
            // Generar el contenido con menos tokens para evitar exceder límites
            $generatedText = $this->aiService->generateText($prompt, [
                'temperature' => 0.9,
                'max_tokens' => 2000, // Reducido para lotes pequeños
            ]);

            Log::info("Respuesta de IA recibida. Longitud: " . strlen($generatedText) . " caracteres");
            Log::debug("Primeros 500 caracteres de la respuesta: " . substr($generatedText, 0, 500));

            // Parsear la respuesta
            $parsedQuotes = $this->parseQuotesFromResponse($generatedText, $count);
            
            Log::info("Se parsearon " . count($parsedQuotes) . " frases del lote");
            
            return $parsedQuotes;
        } catch (Exception $e) {
            Log::error("Error en generateQuotesBatch: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Construye el prompt para la IA
     */
    private function buildPrompt(int $count, int $startDay): string
    {
        return "Genera exactamente {$count} frases estoicas diarias únicas e inspiradoras. 

Cada frase debe seguir este formato JSON estricto:
{
  \"quotes\": [
    {
      \"quote\": \"Texto de la frase estoica\",
      \"author\": \"Nombre del filósofo estoico (ej: Marco Aurelio, Séneca, Epicteto, etc.)\",
      \"category\": \"Categoría (ej: Sabiduría, Resiliencia, Virtud, Autocontrol, Aceptación, etc.)\"
    },
    ...
  ]
}

Requisitos:
- Todas las frases deben ser únicas y diferentes
- Deben ser frases estoicas auténticas o inspiradas en filosofía estoica
- Los autores deben ser filósofos estoicos reconocidos
- Las categorías deben variar (Sabiduría, Resiliencia, Virtud, Autocontrol, Aceptación, Perseverancia, etc.)
- Responde SOLO con el JSON, sin texto adicional antes o después
- Genera exactamente {$count} frases";
    }

    /**
     * Parsea la respuesta de la IA y extrae las frases
     */
    private function parseQuotesFromResponse(string $response, int $expectedCount): array
    {
        $quotes = [];

        Log::info("Parseando respuesta de IA. Longitud: " . strlen($response) . " caracteres");

        // Intentar extraer JSON de la respuesta
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/', $response, $jsonMatch)) {
            $json = json_decode($jsonMatch[0], true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Error al decodificar JSON: " . json_last_error_msg());
            }
            
            if (isset($json['quotes']) && is_array($json['quotes'])) {
                $quotes = $json['quotes'];
                Log::info("Se encontraron " . count($quotes) . " frases en el JSON");
            } else {
                Log::warning("El JSON no contiene la clave 'quotes' o no es un array");
                Log::debug("Claves encontradas en JSON: " . (is_array($json) ? implode(', ', array_keys($json)) : 'No es array'));
            }
        } else {
            Log::warning("No se encontró JSON en la respuesta");
        }

        // Si no se encontró JSON válido, intentar parsear línea por línea
        if (empty($quotes)) {
            Log::info("Intentando parsear desde texto plano...");
            $quotes = $this->parseQuotesFromText($response);
            Log::info("Se encontraron " . count($quotes) . " frases en el texto plano");
        }

        // Validar estructura de cada frase
        $validQuotes = [];
        foreach ($quotes as $index => $quote) {
            if (isset($quote['quote']) && isset($quote['author']) && isset($quote['category'])) {
                $validQuotes[] = [
                    'quote' => trim($quote['quote']),
                    'author' => trim($quote['author']),
                    'category' => trim($quote['category'])
                ];
            } else {
                Log::warning("Frase en índice {$index} no tiene la estructura correcta. Claves: " . (is_array($quote) ? implode(', ', array_keys($quote)) : 'No es array'));
            }
        }

        Log::info("Frases válidas después de validación: " . count($validQuotes) . " de " . count($quotes) . " totales");

        // Si no tenemos suficientes frases, lanzar excepción
        if (count($validQuotes) < $expectedCount) {
            Log::warning("Se generaron " . count($validQuotes) . " frases válidas, se esperaban {$expectedCount}");
            
            // Si faltan muchas, lanzar excepción
            if (count($validQuotes) < $expectedCount * 0.5) {
                throw new Exception("No se generaron suficientes frases. Se esperaban {$expectedCount}, se obtuvieron " . count($validQuotes) . ". Respuesta de IA: " . substr($response, 0, 200));
            }
        }

        // Retornar solo las que necesitamos
        return array_slice($validQuotes, 0, $expectedCount);
    }

    /**
     * Parsea frases desde texto plano (fallback)
     */
    private function parseQuotesFromText(string $text): array
    {
        $quotes = [];
        $lines = explode("\n", $text);

        $currentQuote = null;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Detectar patrones comunes
            if (preg_match('/["\'](.+)["\']\s*[-–]\s*(.+)/', $line, $matches)) {
                $quotes[] = [
                    'quote' => $matches[1],
                    'author' => $matches[2],
                    'category' => 'Sabiduría' // Default
                ];
            }
        }

        return $quotes;
    }

    /**
     * Determina si un año es bisiesto
     */
    private function isLeapYear(int $year): bool
    {
        return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
    }

    /**
     * Extrae el tiempo de espera recomendado del mensaje de error de Gemini
     */
    private function extractRetryDelay(string $errorMessage): int
    {
        // Intentar extraer el retryDelay del JSON de error
        if (preg_match('/"retryDelay":\s*"(\d+)s"/', $errorMessage, $matches)) {
            return (int) $matches[1] + 2; // Agregar 2 segundos de margen
        }
        
        // Intentar extraer "Please retry in X.XXs"
        if (preg_match('/Please retry in ([\d.]+)s/i', $errorMessage, $matches)) {
            return (int) ceil((float) $matches[1]) + 2; // Redondear hacia arriba y agregar margen
        }
        
        // Si no se encuentra, usar un tiempo por defecto más largo
        Log::warning("No se pudo extraer retryDelay del error. Usando 30 segundos por defecto.");
        return 30;
    }
}

