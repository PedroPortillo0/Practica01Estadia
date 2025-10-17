<?php

namespace App\Domain\Enums\FilosofiaDeVida;

enum LifePurpose: string
{
    case AYUDAR_OTROS = 'ayudar_otros';
    case DESARROLLAR_POTENCIAL = 'desarrollar_potencial';
    case ENCONTRAR_PAZ = 'encontrar_paz';
    case CREAR_DURADERO = 'crear_duradero';
    case VIVIR_EXPERIENCIAS = 'vivir_experiencias';
    case AUN_DESCUBRIENDO = 'aun_descubriendo';

    public function getLabel(): string
    {
        return match($this) {
            self::AYUDAR_OTROS => 'Ayudar a otros y contribuir al bien común',
            self::DESARROLLAR_POTENCIAL => 'Desarrollar mi potencial personal al máximo',
            self::ENCONTRAR_PAZ => 'Encontrar paz interior y sabiduría',
            self::CREAR_DURADERO => 'Crear algo duradero y significativo',
            self::VIVIR_EXPERIENCIAS => 'Vivir experiencias plenas y auténticas',
            self::AUN_DESCUBRIENDO => 'Aún estoy descubriéndolo',
        };
    }
}
