<?php

namespace App\Application\UseCases;

use App\Domain\Ports\DailyQuoteRepositoryInterface;
use Exception;

class GetDailyQuote
{
    private DailyQuoteRepositoryInterface $repository;

    public function __construct(DailyQuoteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtiene la frase del día basada en el día del año
     * 
     * @param bool $includeDetail Si debe incluir información completa
     * @return array
     */
    public function execute(bool $includeDetail = false): array
    {
        try {
            // Calcular el día del año (1-366)
            $dayOfYear = (int) date('z') + 1; // date('z') retorna 0-365, lo convertimos a 1-366
            
            // Buscar la frase específica para este día
            $quoteEntity = $this->repository->findByDayOfYear($dayOfYear);

            if (!$quoteEntity) {
                throw new Exception('No hay frase disponible para el día de hoy');
            }

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
                        'is_active' => $quoteEntity->isActive()
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
                    'date' => date('Y-m-d')
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

