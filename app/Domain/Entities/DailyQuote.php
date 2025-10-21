<?php

namespace App\Domain\Entities;

class DailyQuote
{
    private ?int $id;
    private string $quote;
    private string $author;
    private string $category;
    private int $dayOfYear;
    private bool $isActive;

    public function __construct(
        string $quote,
        string $author,
        string $category,
        int $dayOfYear,
        bool $isActive = true,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->quote = $quote;
        $this->author = $author;
        $this->category = $category;
        $this->dayOfYear = $dayOfYear;
        $this->isActive = $isActive;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuote(): string
    {
        return $this->quote;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getDayOfYear(): int
    {
        return $this->dayOfYear;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setQuote(string $quote): void
    {
        $this->quote = $quote;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function setCategory(string $category): void
    {
        $this->category = $category;
    }

    public function setDayOfYear(int $dayOfYear): void
    {
        $this->dayOfYear = $dayOfYear;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'quote' => $this->quote,
            'author' => $this->author,
            'category' => $this->category,
            'day_of_year' => $this->dayOfYear,
            'is_active' => $this->isActive
        ];
    }
}

