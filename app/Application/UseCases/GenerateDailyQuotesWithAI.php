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
     * @return array Resultado de la operación
     */
    public function execute(?int $year = null): array
    {
        try {
            // Determinar si el año es bisiesto
            if ($year === null) {
                $year = (int) date('Y');
            }
            
            $isLeapYear = $this->isLeapYear($year);
            $totalDays = $isLeapYear ? 366 : 365;

            Log::info("Iniciando generación de {$totalDays} frases diarias para el año {$year}");

            // Generar todas las frases
            $quotes = $this->generateQuotes($totalDays);

            // Guardar en la base de datos
            $saved = 0;
            $errors = 0;

            foreach ($quotes as $index => $quoteData) {
                try {
                    $dayOfYear = $index + 1; // Día del año (1-365 o 1-366)

                    // Verificar si ya existe una frase para este día
                    $existing = $this->quoteRepository->findByDayOfYear($dayOfYear);
                    if ($existing) {
                        Log::warning("Ya existe una frase para el día {$dayOfYear}, se omitirá");
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

                    // Guardar
                    $this->quoteRepository->save($quote);
                    $saved++;

                    Log::info("Frase guardada para el día {$dayOfYear}");

                } catch (Exception $e) {
                    $errors++;
                    Log::error("Error guardando frase para el día " . ($index + 1) . ": " . $e->getMessage());
                }
            }

            return [
                'success' => true,
                'message' => "Generación completada: {$saved} frases guardadas, {$errors} errores",
                'total_days' => $totalDays,
                'saved' => $saved,
                'errors' => $errors,
                'year' => $year,
                'is_leap_year' => $isLeapYear
            ];

        } catch (Exception $e) {
            Log::error('Error generando frases con IA: ' . $e->getMessage());
            throw new Exception('Error al generar frases diarias: ' . $e->getMessage());
        }
    }

    /**
     * Genera las frases usando IA
     */
    private function generateQuotes(int $totalDays): array
    {
        // Crear un prompt que genere todas las frases de una vez
        $prompt = $this->buildPrompt($totalDays);

        Log::info("Generando {$totalDays} frases con IA...");

        // Generar el contenido
        $generatedText = $this->aiService->generateText($prompt, [
            'temperature' => 0.9,
            'max_tokens' => 8000, // Aumentar para generar todas las frases
        ]);

        // Parsear la respuesta
        return $this->parseQuotesFromResponse($generatedText, $totalDays);
    }

    /**
     * Construye el prompt para la IA
     */
    private function buildPrompt(int $totalDays): string
    {
        return "Genera exactamente {$totalDays} frases estoicas diarias únicas e inspiradoras. 

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
- Genera exactamente {$totalDays} frases";
    }

    /**
     * Parsea la respuesta de la IA y extrae las frases
     */
    private function parseQuotesFromResponse(string $response, int $expectedCount): array
    {
        $quotes = [];

        // Intentar extraer JSON de la respuesta
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/', $response, $jsonMatch)) {
            $json = json_decode($jsonMatch[0], true);
            
            if (isset($json['quotes']) && is_array($json['quotes'])) {
                $quotes = $json['quotes'];
            }
        }

        // Si no se encontró JSON válido, intentar parsear línea por línea
        if (empty($quotes)) {
            $quotes = $this->parseQuotesFromText($response);
        }

        // Validar que tenemos el número correcto de frases
        if (count($quotes) < $expectedCount) {
            Log::warning("Se generaron " . count($quotes) . " frases, se esperaban {$expectedCount}");
            
            // Si faltan muchas, generar las que faltan
            if (count($quotes) < $expectedCount * 0.5) {
                throw new Exception("No se generaron suficientes frases. Se esperaban {$expectedCount}, se obtuvieron " . count($quotes));
            }
        }

        // Validar estructura de cada frase
        $validQuotes = [];
        foreach ($quotes as $quote) {
            if (isset($quote['quote']) && isset($quote['author']) && isset($quote['category'])) {
                $validQuotes[] = [
                    'quote' => trim($quote['quote']),
                    'author' => trim($quote['author']),
                    'category' => trim($quote['category'])
                ];
            }
        }

        return $validQuotes;
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
}

