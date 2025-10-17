<?php

namespace App\Domain\Enums\DatosPersonales;

enum SexualOrientation: string
{
    // Opciones principales
    case HETEROSEXUAL = 'heterosexual';
    case HOMOSEXUAL = 'homosexual';
    
    // Opciones adicionales (Ver más)
    case BISEXUAL = 'bisexual';
    case PANSEXUAL = 'pansexual';
    case ASEXUAL = 'asexual';
    case DEMISEXUAL = 'demisexual';
    case PREFIERO_NO_DECIR = 'prefiero_no_decir';

    public function getLabel(): string
    {
        return match($this) {
            self::HETEROSEXUAL => 'Heterosexual',
            self::HOMOSEXUAL => 'Homosexual',
            self::BISEXUAL => 'Bisexual',
            self::PANSEXUAL => 'Pansexual',
            self::ASEXUAL => 'Asexual',
            self::DEMISEXUAL => 'Demisexual',
            self::PREFIERO_NO_DECIR => 'Prefiero no decir',
        };
    }
    
    public static function getMainOptions(): array
    {
        return [
            ['value' => self::HETEROSEXUAL->value, 'label' => self::HETEROSEXUAL->getLabel()],
            ['value' => self::HOMOSEXUAL->value, 'label' => self::HOMOSEXUAL->getLabel()],
        ];
    }
    
    public static function getAdditionalOptions(): array
    {
        return [
            ['value' => self::HETEROSEXUAL->value, 'label' => self::HETEROSEXUAL->getLabel()],
            ['value' => self::HOMOSEXUAL->value, 'label' => self::HOMOSEXUAL->getLabel()],
            ['value' => self::BISEXUAL->value, 'label' => self::BISEXUAL->getLabel()],
            ['value' => self::PANSEXUAL->value, 'label' => self::PANSEXUAL->getLabel()],
            ['value' => self::ASEXUAL->value, 'label' => self::ASEXUAL->getLabel()],
            ['value' => self::DEMISEXUAL->value, 'label' => self::DEMISEXUAL->getLabel()],
            ['value' => self::PREFIERO_NO_DECIR->value, 'label' => self::PREFIERO_NO_DECIR->getLabel()],
        ];
    }
}
