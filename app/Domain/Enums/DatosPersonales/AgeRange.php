<?php

namespace App\Domain\Enums\DatosPersonales;

enum AgeRange: string
{
    case RANGE_18_25 = '18-25';
    case RANGE_26_35 = '26-35';
    case RANGE_36_45 = '36-45';
    case RANGE_46_55 = '46-55';
    case RANGE_56_65 = '56-65';
    case RANGE_65_PLUS = '65+';

    public function getLabel(): string
    {
        return match($this) {
            self::RANGE_18_25 => '18 - 25',
            self::RANGE_26_35 => '26 - 35',
            self::RANGE_36_45 => '36 - 45',
            self::RANGE_46_55 => '46 - 55',
            self::RANGE_56_65 => '56 - 65',
            self::RANGE_65_PLUS => '65 +',
        };
    }
}
