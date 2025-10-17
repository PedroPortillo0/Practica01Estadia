<?php

namespace App\Domain\Enums\FilosofiaDeVida;

enum AdversityResponse: string
{
    case OPORTUNIDAD_APRENDIZAJE = 'oportunidad_aprendizaje';
    case ACEPTACION_PACIENCIA = 'aceptacion_paciencia';
    case BUSCAR_LECCIONES = 'buscar_lecciones';
    case MANTENER_PRINCIPIOS = 'mantener_principios';
    case ADAPTARSE_NUEVOS_CAMINOS = 'adaptarse_nuevos_caminos';
    case APOYO_OTROS_REFLEXION = 'apoyo_otros_reflexion';

    public function getLabel(): string
    {
        return match($this) {
            self::OPORTUNIDAD_APRENDIZAJE => 'Como oportunidad de aprendizaje y crecimiento',
            self::ACEPTACION_PACIENCIA => 'Con aceptación y paciencia',
            self::BUSCAR_LECCIONES => 'Buscando lecciones y sabiduría',
            self::MANTENER_PRINCIPIOS => 'Manteniéndome firmes a mis principios',
            self::ADAPTARSE_NUEVOS_CAMINOS => 'Adaptándome y encontrando nuevos caminos',
            self::APOYO_OTROS_REFLEXION => 'Con apoyo de otros y reflexión personal',
        };
    }
}
