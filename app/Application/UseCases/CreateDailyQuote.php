<?php

namespace App\Application\UseCases;

use App\Domain\Entities\DailyQuote;
use App\Domain\Ports\DailyQuoteRepositoryInterface;

class CreateDailyQuote
{
    private DailyQuoteRepositoryInterface $repository;

    public function __construct(DailyQuoteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $data): array
    {
        // Validar datos
        $this->validate($data);

        // Crear entidad de dominio
        $quote = new DailyQuote(
            $data['quote'],
            $data['author'],
            $data['category'],
            $data['day_of_year'],
            $data['is_active'] ?? true
        );

        // Guardar en repositorio
        $savedQuote = $this->repository->save($quote);

        return [
            'success' => true,
            'message' => 'Frase creada exitosamente',
            'data' => $savedQuote->toArray()
        ];
    }

    private function validate(array $data): void
    {
        if (empty($data['quote'])) {
            throw new \InvalidArgumentException('La frase es requerida');
        }

        if (empty($data['author'])) {
            throw new \InvalidArgumentException('El autor es requerido');
        }

        if (empty($data['category'])) {
            throw new \InvalidArgumentException('La categoría es requerida');
        }

        if (!isset($data['day_of_year']) || $data['day_of_year'] < 1 || $data['day_of_year'] > 366) {
            throw new \InvalidArgumentException('El día del año debe estar entre 1 y 366');
        }
    }
}

