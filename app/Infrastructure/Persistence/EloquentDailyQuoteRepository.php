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
    
    public function findAllPaginated(int $page, int $limit, ?string $category = null, ?string $search = null): array
    {
        $offset = ($page - 1) * $limit;
        
        // Construir query base
        $query = DailyQuote::query();
        
        // Aplicar filtro por categoría si se proporciona
        if ($category && $category !== '') {
            $query->where('category', $category);
        }
        
        // Aplicar búsqueda general (frase y autor)
        if ($search && $search !== '') {
            $searchTerm = '%' . $search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('quote', 'LIKE', $searchTerm)
                  ->orWhere('author', 'LIKE', $searchTerm);
            });
        }
        
        // Contar total con filtros aplicados
        $total = $query->count();
        
        // Obtener frases con paginación y filtros
        $quotes = $query->orderBy('day_of_year')
            ->offset($offset)
            ->limit($limit)
            ->get();
        
        return [
            'data' => $quotes->map(function ($quote) {
                return $this->toDomainEntity($quote);
            })->toArray(),
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ];
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
    
    public function reorderAfterDelete(int $deletedDayOfYear): void
    {
        // Obtener todas las frases con day_of_year mayor al eliminado
        $quotesToReorder = DailyQuote::where('day_of_year', '>', $deletedDayOfYear)
            ->orderBy('day_of_year')
            ->get();
        
        // Actualizar cada frase para que su day_of_year sea (day_of_year - 1)
        foreach ($quotesToReorder as $quote) {
            $quote->update(['day_of_year' => $quote->day_of_year - 1]);
        }
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

