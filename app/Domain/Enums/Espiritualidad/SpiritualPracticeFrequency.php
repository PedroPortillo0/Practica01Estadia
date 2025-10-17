<?php

namespace App\Domain\Enums\Espiritualidad;

enum SpiritualPracticeFrequency: string
{
    case DIARIAMENTE = 'diariamente';
    case SEMANALMENTE = 'semanalmente';
    case OCASIONALMENTE = 'ocasionalmente';
    case NUNCA = 'nunca';

    public function getLabel(): string
    {
        return match($this) {
            self::DIARIAMENTE => 'Diariamente',
            self::SEMANALMENTE => 'Semanalmente',
            self::OCASIONALMENTE => 'Ocasionalmente',
            self::NUNCA => 'Nunca',
        };
    }
}
