<?php

namespace App\Application\UseCases;

use App\Domain\Ports\AIServiceInterface;
use App\Models\UserQuizResponse;
use App\Domain\Enums\Espiritualidad\ReligiousBelief;
use App\Domain\Enums\Espiritualidad\SpiritualPracticeLevel;
use App\Domain\Enums\Espiritualidad\SpiritualPracticeFrequency;
use App\Domain\Enums\DesafiosDiarios\DailyChallenge;
use App\Domain\Enums\CaminoEstoico\StoicPath;
use Illuminate\Support\Facades\Log;
use Exception;

class GeneratePersonalizedQuoteExplanation
{
    private AIServiceInterface $aiService;

    public function __construct(AIServiceInterface $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Genera una frase personalizada y su explicación basada en la frase del día y el perfil del usuario
     * 
     * @param array $dailyQuote Datos de la frase del día (quote, author, category)
     * @param UserQuizResponse $userQuiz Datos del quiz del usuario
     * @return array Frase personalizada y explicación
     */
    public function execute(array $dailyQuote, UserQuizResponse $userQuiz): array
    {
        try {
            // Preparar contexto del usuario para el prompt
            $userContext = $this->buildUserContext($userQuiz);

            // Construir el prompt para la IA
            $prompt = $this->buildPrompt($dailyQuote, $userContext);

            Log::info("Generando frase personalizada para usuario: {$userQuiz->user_id}");

            // Generar contenido con IA
            $generatedText = $this->aiService->generateText($prompt, [
                'temperature' => 0.8,
                'max_tokens' => 1500,
            ]);

            // Parsear la respuesta
            $result = $this->parseResponse($generatedText, $dailyQuote);

            return [
                'success' => true,
                'data' => [
                    'personalized_quote' => $result['personalized_quote'],
                    'explanation' => $result['explanation'],
                    'original_author' => $dailyQuote['author'],
                    'original_category' => $dailyQuote['category'],
                    'is_personalized' => true
                ]
            ];

        } catch (Exception $e) {
            Log::error('Error generando frase personalizada: ' . $e->getMessage());
            throw new Exception('Error al generar frase personalizada: ' . $e->getMessage());
        }
    }

    /**
     * Construye el contexto del usuario a partir de su quiz
     */
    private function buildUserContext(UserQuizResponse $userQuiz): array
    {
        // Obtener labels de los enums
        $religiousBelief = ReligiousBelief::tryFrom($userQuiz->religious_belief);
        $spiritualLevel = SpiritualPracticeLevel::tryFrom($userQuiz->spiritual_practice_level);
        $spiritualFrequency = SpiritualPracticeFrequency::tryFrom($userQuiz->spiritual_practice_frequency);

        // Obtener desafíos diarios con sus labels
        $dailyChallenges = [];
        if (is_array($userQuiz->daily_challenges)) {
            foreach ($userQuiz->daily_challenges as $challengeValue) {
                $challenge = DailyChallenge::tryFrom($challengeValue);
                if ($challenge) {
                    $dailyChallenges[] = $challenge->getLabel();
                }
            }
        }

        // Obtener caminos estoicos con sus labels y descripciones
        $stoicPaths = [];
        if (is_array($userQuiz->stoic_paths)) {
            foreach ($userQuiz->stoic_paths as $pathValue) {
                $path = StoicPath::tryFrom($pathValue);
                if ($path) {
                    $stoicPaths[] = [
                        'name' => $path->getLabel(),
                        'description' => $path->getDescription()
                    ];
                }
            }
        }

        return [
            'religious_belief' => $religiousBelief ? $religiousBelief->getLabel() : $userQuiz->religious_belief,
            'spiritual_practice_level' => $spiritualLevel ? $spiritualLevel->getLabel() : $userQuiz->spiritual_practice_level,
            'spiritual_practice_frequency' => $spiritualFrequency ? $spiritualFrequency->getLabel() : $userQuiz->spiritual_practice_frequency,
            'daily_challenges' => $dailyChallenges,
            'stoic_paths' => $stoicPaths,
            'age_range' => $userQuiz->age_range,
            'gender' => $userQuiz->gender,
            'country' => $userQuiz->country
        ];
    }

    /**
     * Construye el prompt para la IA
     */
    private function buildPrompt(array $dailyQuote, array $userContext): string
    {
        $challengesText = implode(', ', $userContext['daily_challenges']);
        $pathsText = implode(', ', array_map(fn($p) => $p['name'], $userContext['stoic_paths']));
        $pathsDescriptions = implode("\n- ", array_map(fn($p) => "{$p['name']}: {$p['description']}", $userContext['stoic_paths']));

        return "Eres un mentor estoico experto. Tu tarea es crear una NUEVA frase estoica única y personalizada, mezclando la sabiduría de la frase del día con el perfil completo del usuario.

⚠️ REGLA CRÍTICA: La frase que generes DEBE SER COMPLETAMENTE DIFERENTE a la frase original. NO repitas, NO adaptes, NO parafrasees. Crea una frase NUEVA inspirada en la sabiduría pero con palabras y estructura completamente diferentes.

FRASE ORIGINAL DEL DÍA (usa solo como inspiración conceptual, NO la copies):
\"{$dailyQuote['quote']}\"
- Autor: {$dailyQuote['author']}
- Categoría: {$dailyQuote['category']}

PERFIL DEL USUARIO (usa esta información para crear la frase personalizada):
- Creencia religiosa: {$userContext['religious_belief']}
- Nivel de práctica espiritual: {$userContext['spiritual_practice_level']}
- Frecuencia de práctica espiritual: {$userContext['spiritual_practice_frequency']}
- Desafíos diarios que quiere trabajar: {$challengesText}
- Caminos estoicos elegidos: {$pathsText}
- Descripción de sus caminos estoicos:
  - {$pathsDescriptions}
- Rango de edad: {$userContext['age_range']}
- Género: {$userContext['gender']}
- País: " . ($userContext['country'] ?? 'No especificado') . "

INSTRUCCIONES:
1. CREA UNA FRASE ESTOICA COMPLETAMENTE NUEVA que:
   - Tenga un mensaje similar en sabiduría a la frase original, pero con palabras y estructura TOTALMENTE DIFERENTES
   - Refleje los caminos estoicos específicos del usuario: {$pathsText}
   - Sea relevante para sus desafíos diarios: {$challengesText}
   - Respete sus creencias religiosas: {$userContext['religious_belief']}
   - Sea apropiada para su nivel de práctica espiritual: {$userContext['spiritual_practice_level']}
   - Mantenga el estilo y profundidad de la filosofía estoica
   - Sea única, original y personalizada para este usuario específico
   - NO use las mismas palabras, estructura o frases de la original

2. ESCRIBE UNA EXPLICACIÓN MODERADA (entre 120-180 palabras) en TONO SERIO que:
   - Explique el significado de la NUEVA frase personalizada de forma clara y concreta
   - Conecte la enseñanza con los caminos estoicos específicos del usuario
   - Relacione la sabiduría con sus desafíos diarios concretos
   - Muestre cómo aplicar esta enseñanza en su vida diaria de forma práctica
   - Use un TONO SERIO pero accesible, profesional y reflexivo
   - Sea CONCRETA, FÁCIL DE ENTENDER y bien estructurada
   - Evite ser demasiado larga o demasiado corta (longitud moderada)
   - Considere su nivel de práctica espiritual
   - Respete sus creencias religiosas

FORMATO DE RESPUESTA (JSON estricto, sin markdown, sin texto adicional):
{
  \"personalized_quote\": \"La frase estoica NUEVA y personalizada (diferente a la original)\",
  \"explanation\": \"Explicación moderada, concreta y fácil de entender (entre 120-180 palabras)\"
}

RECUERDA:
- La frase DEBE ser diferente a: \"{$dailyQuote['quote']}\"
- Crea una frase NUEVA con el mismo espíritu pero palabras diferentes
- El tono debe ser SERIO y reflexivo
- Responde SOLO con el JSON válido";
    }

    /**
     * Parsea la respuesta de la IA
     */
    private function parseResponse(string $response, array $dailyQuote): array
    {
        // Log de la respuesta cruda para debugging
        Log::info('Respuesta cruda de IA:', ['response' => $response]);
        
        // Limpiar la respuesta: remover markdown code blocks si existen
        $cleanedResponse = $response;
        $cleanedResponse = preg_replace('/```json\s*/', '', $cleanedResponse);
        $cleanedResponse = preg_replace('/```\s*/', '', $cleanedResponse);
        $cleanedResponse = trim($cleanedResponse);
        
        // Intentar parsear como JSON directamente
        $json = json_decode($cleanedResponse, true);
        
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            if (isset($json['personalized_quote']) && isset($json['explanation'])) {
                $personalizedQuote = trim($json['personalized_quote']);
                $explanation = trim($json['explanation']);
                
                // Validar que la frase personalizada sea diferente a la original
                if ($personalizedQuote === $dailyQuote['quote']) {
                    Log::warning('La frase personalizada es igual a la original. La IA no generó una nueva frase.');
                    throw new Exception('La IA no generó una frase personalizada diferente. Por favor, intenta de nuevo.');
                }
                
                // Validar que la explicación tenga longitud moderada (entre 120-180 palabras aprox)
                $wordCount = str_word_count($explanation);
                if ($wordCount < 100) {
                    Log::warning('La explicación es demasiado corta: ' . $wordCount . ' palabras');
                } elseif ($wordCount > 250) {
                    Log::info('La explicación es más larga de lo esperado: ' . $wordCount . ' palabras (se esperaba 120-180)');
                }
                
                return [
                    'personalized_quote' => $personalizedQuote,
                    'explanation' => $explanation
                ];
            }
        }
        
        // Intentar extraer JSON con regex si el parseo directo falló
        $jsonMatch = [];
        if (preg_match('/\{[\s\S]*\}/', $cleanedResponse, $jsonMatch)) {
            $json = json_decode($jsonMatch[0], true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                if (isset($json['personalized_quote']) && isset($json['explanation'])) {
                    $personalizedQuote = trim($json['personalized_quote']);
                    $explanation = trim($json['explanation']);
                    
                    // Validar que la frase personalizada sea diferente
                    if ($personalizedQuote === $dailyQuote['quote']) {
                        Log::warning('La frase personalizada es igual a la original (regex).');
                        throw new Exception('La IA no generó una frase personalizada diferente. Por favor, intenta de nuevo.');
                    }
                    
                    return [
                        'personalized_quote' => $personalizedQuote,
                        'explanation' => $explanation
                    ];
                }
            }
        }

        // Si llegamos aquí, no se pudo parsear
        Log::error('No se pudo parsear la respuesta de IA', [
            'response' => $response,
            'cleaned' => $cleanedResponse,
            'json_error' => json_last_error_msg()
        ]);
        
        throw new Exception('No se pudo parsear la respuesta de la IA. La respuesta no tiene el formato JSON esperado.');
    }
}

