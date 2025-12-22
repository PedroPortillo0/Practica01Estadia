<?php

namespace App\Domain\Enums\NivelEstoico;

enum StoicLevel: string
{
    case PRINCIPIANTE = 'principiante';
    case INTERMEDIO = 'intermedio';
    case AVANZADO = 'avanzado';

    /**
     * Obtiene la etiqueta legible del nivel estoico
     */
    public function getLabel(): string
    {
        return match($this) {
            self::PRINCIPIANTE => 'Principiante',
            self::INTERMEDIO => 'Intermedio',
            self::AVANZADO => 'Avanzado',
        };
    }
}
