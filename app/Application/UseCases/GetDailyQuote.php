<?php

namespace App\Application\UseCases;

use App\Domain\Ports\DailyQuoteRepositoryInterface;
use App\Application\UseCases\GeneratePersonalizedQuoteExplanation;
use App\Domain\Ports\UserRepositoryInterface;
use App\Models\UserQuizResponse;
use Exception;

class GetDailyQuote
{
    private DailyQuoteRepositoryInterface $repository;
    private ?GeneratePersonalizedQuoteExplanation $personalizeQuote;
    private ?UserRepositoryInterface $userRepository;

    public function __construct(
        DailyQuoteRepositoryInterface $repository,
        ?GeneratePersonalizedQuoteExplanation $personalizeQuote = null,
        ?UserRepositoryInterface $userRepository = null
    ) {
        $this->repository = $repository;
        $this->personalizeQuote = $personalizeQuote;
        $this->userRepository = $userRepository;
    }

    /**
     * Obtiene la frase del día basada en el día del año
     * Si el usuario tiene quiz completo, genera una frase personalizada con IA
     *
     * @param bool $includeDetail Si debe incluir información completa
     * @param string|null $userId ID del usuario autenticado (opcional)
     * @return array
     */
    public function execute(bool $includeDetail = false, ?string $userId = null): array
    {
        try {
            // Calcular el día del año (1-366)
            $dayOfYear = (int) date('z') + 1; // date('z') retorna 0-365, lo convertimos a 1-366

            // Buscar la frase para este día
            $quoteEntity = $this->repository->findByDayOfYear($dayOfYear);

            if (!$quoteEntity) {
                throw new Exception('No hay frase disponible para el día de hoy');
            }

            // Preparar datos de la frase del día
            $dailyQuoteData = [
                'quote' => $quoteEntity->getQuote(),
                'author' => $quoteEntity->getAuthor(),
                'category' => $quoteEntity->getCategory(),
            ];

            // Verificar si el usuario tiene quiz completo y generar personalización
            if ($userId && $this->userRepository && $this->personalizeQuote) {
                $user = $this->userRepository->findById($userId);
                
                if ($user && $user->isQuizCompleted()) {
                    // Obtener datos del quiz del usuario
                    $userQuiz = UserQuizResponse::where('user_id', $userId)->first();
                    
                    if ($userQuiz) {
                        // Generar frase personalizada con IA
                        $personalizedResult = $this->personalizeQuote->execute($dailyQuoteData, $userQuiz);
                        
                        if ($personalizedResult['success']) {
                            // Retornar solo la frase personalizada (no la original)
                            return [
                                'success' => true,
                                'data' => array_merge($personalizedResult['data'], [
                                    'id' => $quoteEntity->getId(),
                                    'date' => date('Y-m-d'),
                                    'day_of_year' => $dayOfYear,
                                ])
                            ];
                        }
                    }
                }
            }

            // Si no tiene quiz completo o hay error, devolver frase normal
            // Preparar respuesta según si se pide detalle o no
            if ($includeDetail) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $quoteEntity->getId(),
                        'quote' => $quoteEntity->getQuote(),
                        'author' => $quoteEntity->getAuthor(),
                        'category' => $quoteEntity->getCategory(),
                        'date' => date('Y-m-d'),
                        'day_of_year' => $dayOfYear,
                        'is_active' => $quoteEntity->isActive(),
                        'is_personalized' => false
                    ]
                ];
            }

            // Respuesta simple para el dashboard
            return [
                'success' => true,
                'data' => [
                    'id' => $quoteEntity->getId(),
                    'quote' => $quoteEntity->getQuote(),
                    'author' => $quoteEntity->getAuthor(),
                    'category' => $quoteEntity->getCategory(),
                    'date' => date('Y-m-d'),
                    'is_personalized' => false
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener la frase del día: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtiene todas las frases (útil para testing o admin)
     * 
     * @return array
     */
    public function getAllQuotes(): array
    {
        try {
            $quotes = $this->repository->findAllActive();

            $quotesArray = array_map(function ($quote) {
                return $quote->toArray();
            }, $quotes);

            return [
                'success' => true,
                'data' => $quotesArray,
                'total' => count($quotesArray)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener las frases: ' . $e->getMessage()
            ];
        }
    }
}

