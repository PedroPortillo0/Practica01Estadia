<?php

namespace App\Domain\Enums\Espiritualidad;

enum SpiritualPracticeLevel: string
{
    case MUY_ACTIVA = 'muy_activa';
    case MODERADA = 'moderada';
    case OCASIONAL = 'ocasional';
    case NO_PRACTICO = 'no_practico';

    public function getLabel(): string
    {
        return match($this) {
            self::MUY_ACTIVA => 'Muy activa',
            self::MODERADA => 'Moderada',
            self::OCASIONAL => 'Ocasional',
            self::NO_PRACTICO => 'No practico',
        };
    }
}
