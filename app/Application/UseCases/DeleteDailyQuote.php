<?php

namespace App\Application\UseCases;

use App\Domain\Ports\DailyQuoteRepositoryInterface;

class DeleteDailyQuote
{
    private DailyQuoteRepositoryInterface $repository;

    public function __construct(DailyQuoteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): array
    {
        // Verificar que la frase existe
        $quote = $this->repository->findById($id);
        
        if (!$quote) {
            throw new \Exception('Frase no encontrada');
        }

        // Guardar el día del año antes de eliminar para reordenar después
        $deletedDayOfYear = $quote->getDayOfYear();

        // Eliminar
        $deleted = $this->repository->delete($id);

        if (!$deleted) {
            throw new \Exception('Error al eliminar la frase');
        }

        // Reordenar las frases siguientes (día 2 → día 1, día 3 → día 2, etc.)
        $this->repository->reorderAfterDelete($deletedDayOfYear);

        return [
            'success' => true,
            'message' => 'Frase eliminada exitosamente y frases reordenadas'
        ];
    }
}

