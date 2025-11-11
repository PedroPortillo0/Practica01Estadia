<?php

namespace App\Application\UseCases;

use App\Domain\Ports\DailyQuoteRepositoryInterface;
use Exception;

class GetAllDailyQuotes
{
    private DailyQuoteRepositoryInterface $repository;

    public function __construct(DailyQuoteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtiene todas las frases con paginación
     * 
     * @param int $page Número de página (por defecto 1)
     * @param int $limit Cantidad de resultados por página (por defecto 10)
     * @param string|null $category Categoría para filtrar (opcional)
     * @param string|null $search Término de búsqueda general (opcional)
     * @return array
     */
    public function execute(int $page = 1, int $limit = 10, ?string $category = null, ?string $search = null): array
    {
        try {
            $result = $this->repository->findAllPaginated($page, $limit, $category, $search);
            
            $quotesArray = array_map(function ($quote) {
                return $quote->toArray();
            }, $result['data']);

            return [
                'success' => true,
                'message' => 'Frases obtenidas exitosamente.',
                'data' => $quotesArray,
                'total' => $result['total'],
                'page' => $result['page'],
                'limit' => $result['limit']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener frases: ' . $e->getMessage()
            ];
        }
    }
}

