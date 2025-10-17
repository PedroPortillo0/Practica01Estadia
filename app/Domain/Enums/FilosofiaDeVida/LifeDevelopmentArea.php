<?php

namespace App\Domain\Enums\FilosofiaDeVida;

enum LifeDevelopmentArea: string
{
    case AUTOCONTROL_DISCIPLINA = 'autocontrol_disciplina';
    case SABIDURIA_COMPRENSION = 'sabiduria_comprension';
    case COMPASION_EMPATIA = 'compasion_empatia';
    case RESILENCIA_DESAFIOS = 'resilencia_desafios';
    case CLARIDAD_VALORES = 'claridad_valores';
    case VIVIR_PRESENTE = 'vivir_presente';

    public function getLabel(): string
    {
        return match($this) {
            self::AUTOCONTROL_DISCIPLINA => 'Autocontrol y disciplina personal',
            self::SABIDURIA_COMPRENSION => 'Sabiduría y comprensión profunda',
            self::COMPASION_EMPATIA => 'Compasión y empatía para otros',
            self::RESILENCIA_DESAFIOS => 'Resilencia ante los desafíos',
            self::CLARIDAD_VALORES => 'Claridad en mis valores y principios',
            self::VIVIR_PRESENTE => 'Capacidad de vivir en el presente',
        };
    }
}
