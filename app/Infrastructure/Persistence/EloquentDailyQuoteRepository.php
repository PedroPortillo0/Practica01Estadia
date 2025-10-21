<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\DailyQuote as DailyQuoteEntity;
use App\Domain\Ports\DailyQuoteRepositoryInterface;
use App\Models\DailyQuote;

class EloquentDailyQuoteRepository implements DailyQuoteRepositoryInterface
{
    public function findById(int $id): ?DailyQuoteEntity
    {
        $quote = DailyQuote::find($id);
        
        return $quote ? $this->toDomainEntity($quote) : null;
    }
    
    public function findByDayOfYear(int $dayOfYear): ?DailyQuoteEntity
    {
        $quote = DailyQuote::where('day_of_year', $dayOfYear)
            ->where('is_active', true)
            ->first();
        
        return $quote ? $this->toDomainEntity($quote) : null;
    }
    
    public function findAll(): array
    {
        $quotes = DailyQuote::orderBy('day_of_year')->get();
        
        return $quotes->map(function ($quote) {
            return $this->toDomainEntity($quote);
        })->toArray();
    }
    
    public function findAllActive(): array
    {
        $quotes = DailyQuote::where('is_active', true)
            ->orderBy('day_of_year')
            ->get();
        
        return $quotes->map(function ($quote) {
            return $this->toDomainEntity($quote);
        })->toArray();
    }
    
    public function save(DailyQuoteEntity $quoteEntity): DailyQuoteEntity
    {
        $quote = DailyQuote::create([
            'quote' => $quoteEntity->getQuote(),
            'author' => $quoteEntity->getAuthor(),
            'category' => $quoteEntity->getCategory(),
            'day_of_year' => $quoteEntity->getDayOfYear(),
            'is_active' => $quoteEntity->isActive()
        ]);
        
        return $this->toDomainEntity($quote);
    }
    
    public function update(DailyQuoteEntity $quoteEntity): DailyQuoteEntity
    {
        $quote = DailyQuote::findOrFail($quoteEntity->getId());
        
        $quote->update([
            'quote' => $quoteEntity->getQuote(),
            'author' => $quoteEntity->getAuthor(),
            'category' => $quoteEntity->getCategory(),
            'day_of_year' => $quoteEntity->getDayOfYear(),
            'is_active' => $quoteEntity->isActive()
        ]);
        
        return $this->toDomainEntity($quote);
    }
    
    public function delete(int $id): bool
    {
        $quote = DailyQuote::find($id);
        
        if (!$quote) {
            return false;
        }
        
        return $quote->delete();
    }
    
    public function count(): int
    {
        return DailyQuote::count();
    }
    
    private function toDomainEntity(DailyQuote $quote): DailyQuoteEntity
    {
        return new DailyQuoteEntity(
            $quote->quote,
            $quote->author,
            $quote->category,
            $quote->day_of_year,
            $quote->is_active,
            $quote->id
        );
    }
}

