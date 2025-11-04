<?php

namespace App\Domain\Ports;

use App\Domain\Entities\DailyQuote;

interface DailyQuoteRepositoryInterface
{
    public function findById(int $id): ?DailyQuote;
    
    public function findByDayOfYear(int $dayOfYear): ?DailyQuote;
    
    public function findAll(): array;
    
    public function findAllActive(): array;
    
    public function findAllPaginated(int $page, int $limit): array;
    
    public function save(DailyQuote $quote): DailyQuote;
    
    public function update(DailyQuote $quote): DailyQuote;
    
    public function delete(int $id): bool;
    
    public function count(): int;
}

