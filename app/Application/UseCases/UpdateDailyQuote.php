<?php

namespace App\Application\UseCases;

use App\Domain\Entities\DailyQuote;
use App\Domain\Ports\DailyQuoteRepositoryInterface;

class UpdateDailyQuote
{
    private DailyQuoteRepositoryInterface $repository;

    public function __construct(DailyQuoteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, array $data): array
    {
        // Verificar que la frase existe
        $existingQuote = $this->repository->findById($id);
        
        if (!$existingQuote) {
            throw new \Exception('Frase no encontrada');
        }

        // Validar datos
        $this->validate($data);

        // Crear entidad actualizada
        $quote = new DailyQuote(
            $data['quote'] ?? $existingQuote->getQuote(),
            $data['author'] ?? $existingQuote->getAuthor(),
            $data['category'] ?? $existingQuote->getCategory(),
            $data['day_of_year'] ?? $existingQuote->getDayOfYear(),
            $data['is_active'] ?? $existingQuote->isActive(),
            $id
        );

        // Actualizar en repositorio
        $updatedQuote = $this->repository->update($quote);

        return [
            'success' => true,
            'message' => 'Frase actualizada exitosamente',
            'data' => $updatedQuote->toArray()
        ];
    }

    private function validate(array $data): void
    {
        if (isset($data['quote']) && empty($data['quote'])) {
            throw new \InvalidArgumentException('La frase no puede estar vacía');
        }

        if (isset($data['author']) && empty($data['author'])) {
            throw new \InvalidArgumentException('El autor no puede estar vacío');
        }

        if (isset($data['category']) && empty($data['category'])) {
            throw new \InvalidArgumentException('La categoría no puede estar vacía');
        }

        if (isset($data['day_of_year']) && ($data['day_of_year'] < 1 || $data['day_of_year'] > 366)) {
            throw new \InvalidArgumentException('El día del año debe estar entre 1 y 366');
        }
    }
}

