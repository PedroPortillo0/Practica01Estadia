<?php

namespace App\Domain\Enums\Espiritualidad;

enum ReligiousBelief: string
{
    case CATOLICO = 'catolico';
    case EVANGELICO = 'evangelico';
    case TESTIGO_DE_JEHOVA = 'testigo_de_jehova';
    case MORMON = 'mormon';
    case JUDIO = 'judio';
    case MUSULMAN = 'musulman';
    case BUDISTA = 'budista';
    case AGNOSTICO_OTRO = 'agnostico_otro';

    public function getLabel(): string
    {
        return match($this) {
            self::CATOLICO => 'Católico',
            self::EVANGELICO => 'Evangélico',
            self::TESTIGO_DE_JEHOVA => 'Testigo de Jehová',
            self::MORMON => 'Mormón',
            self::JUDIO => 'Judío',
            self::MUSULMAN => 'Musulmán',
            self::BUDISTA => 'Budista',
            self::AGNOSTICO_OTRO => 'Agnósticos / otro',
        };
    }
}
