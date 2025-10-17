<?php

namespace App\Domain\Enums\FilosofiaDeVida;

enum HappinessSource: string
{
    case TRANQUILIDAD_MENTAL = 'tranquilidad_mental';
    case RELACIONES_PROFUNDAS = 'relaciones_profundas';
    case CRECIMIENTO_PERSONAL = 'crecimiento_personal';
    case CONTRIBUCION_CAUSAS = 'contribucion_causas';
    case MOMENTOS_CONTEMPLACION = 'momentos_contemplacion';
    case EQUILIBRIO_TRABAJO_DESCANSO = 'equilibrio_trabajo_descanso';

    public function getLabel(): string
    {
        return match($this) {
            self::TRANQUILIDAD_MENTAL => 'La tranquilidad mental y emocional',
            self::RELACIONES_PROFUNDAS => 'Las relaciones profundas y significativas',
            self::CRECIMIENTO_PERSONAL => 'El crecimiento personal constante',
            self::CONTRIBUCION_CAUSAS => 'La contribución a causas importantes',
            self::MOMENTOS_CONTEMPLACION => 'Los momentos de contemplación y reflexión',
            self::EQUILIBRIO_TRABAJO_DESCANSO => 'El equilibrio entre trabajo y descanso',
        };
    }
}
