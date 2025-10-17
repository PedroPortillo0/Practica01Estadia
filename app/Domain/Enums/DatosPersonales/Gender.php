<?php

namespace App\Domain\Enums\DatosPersonales;

enum Gender: string
{
    // Opciones principales
    case MASCULINO = 'masculino';
    case FEMENINO = 'femenino';
    
    // Opciones adicionales (Ver más)
    case NO_BINARIO = 'no_binario';
    case GENERO_FLUIDO = 'genero_fluido';
    case TRANSGENERO = 'transgenero';
    case AGENERO = 'agenero';
    case PREFIERO_NO_DECIR = 'prefiero_no_decir';

    public function getLabel(): string
    {
        return match($this) {
            self::MASCULINO => 'Masculino',
            self::FEMENINO => 'Femenino',
            self::NO_BINARIO => 'No binario',
            self::GENERO_FLUIDO => 'Género fluido',
            self::TRANSGENERO => 'Transgénero',
            self::AGENERO => 'Agénero',
            self::PREFIERO_NO_DECIR => 'Prefiero no decir',
        };
    }
    
    public static function getMainOptions(): array
    {
        return [
            ['value' => self::MASCULINO->value, 'label' => self::MASCULINO->getLabel()],
            ['value' => self::FEMENINO->value, 'label' => self::FEMENINO->getLabel()],
        ];
    }
    
    public static function getAdditionalOptions(): array
    {
        return [
            ['value' => self::MASCULINO->value, 'label' => self::MASCULINO->getLabel()],
            ['value' => self::FEMENINO->value, 'label' => self::FEMENINO->getLabel()],
            ['value' => self::NO_BINARIO->value, 'label' => self::NO_BINARIO->getLabel()],
            ['value' => self::GENERO_FLUIDO->value, 'label' => self::GENERO_FLUIDO->getLabel()],
            ['value' => self::TRANSGENERO->value, 'label' => self::TRANSGENERO->getLabel()],
            ['value' => self::AGENERO->value, 'label' => self::AGENERO->getLabel()],
            ['value' => self::PREFIERO_NO_DECIR->value, 'label' => self::PREFIERO_NO_DECIR->getLabel()],
        ];
    }
}
