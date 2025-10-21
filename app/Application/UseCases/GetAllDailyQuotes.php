<?php

namespace App\Application\UseCases;

use App\Domain\Ports\DailyQuoteRepositoryInterface;

class GetAllDailyQuotes
{
    private DailyQuoteRepositoryInterface $repository;

    public function __construct(DailyQuoteRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(bool $onlyActive = false): array
    {
        $quotes = $onlyActive 
            ? $this->repository->findAllActive() 
            : $this->repository->findAll();

        $quotesArray = array_map(function ($quote) {
            return $quote->toArray();
        }, $quotes);

        return [
            'success' => true,
            'data' => $quotesArray,
            'total' => count($quotesArray)
        ];
    }
}

