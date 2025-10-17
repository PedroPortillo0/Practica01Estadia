<?php

namespace App\Domain\Enums\DatosPersonales;

enum MexicanState: string
{
    case AGUASCALIENTES = 'aguascalientes';
    case BAJA_CALIFORNIA = 'baja_california';
    case BAJA_CALIFORNIA_SUR = 'baja_california_sur';
    case CAMPECHE = 'campeche';
    case CHIAPAS = 'chiapas';
    case CHIHUAHUA = 'chihuahua';
    case CIUDAD_DE_MEXICO = 'ciudad_de_mexico';
    case COAHUILA = 'coahuila';
    case COLIMA = 'colima';
    case DURANGO = 'durango';
    case ESTADO_DE_MEXICO = 'estado_de_mexico';
    case GUANAJUATO = 'guanajuato';
    case GUERRERO = 'guerrero';
    case HIDALGO = 'hidalgo';
    case JALISCO = 'jalisco';
    case MICHOACAN = 'michoacan';
    case MORELOS = 'morelos';
    case NAYARIT = 'nayarit';
    case NUEVO_LEON = 'nuevo_leon';
    case OAXACA = 'oaxaca';
    case PUEBLA = 'puebla';
    case QUERETARO = 'queretaro';
    case QUINTANA_ROO = 'quintana_roo';
    case SAN_LUIS_POTOSI = 'san_luis_potosi';
    case SINALOA = 'sinaloa';
    case SONORA = 'sonora';
    case TABASCO = 'tabasco';
    case TAMAULIPAS = 'tamaulipas';
    case TLAXCALA = 'tlaxcala';
    case VERACRUZ = 'veracruz';
    case YUCATAN = 'yucatan';
    case ZACATECAS = 'zacatecas';

    public function getLabel(): string
    {
        return match($this) {
            self::AGUASCALIENTES => 'Aguascalientes',
            self::BAJA_CALIFORNIA => 'Baja California',
            self::BAJA_CALIFORNIA_SUR => 'Baja California Sur',
            self::CAMPECHE => 'Campeche',
            self::CHIAPAS => 'Chiapas',
            self::CHIHUAHUA => 'Chihuahua',
            self::CIUDAD_DE_MEXICO => 'Ciudad de México',
            self::COAHUILA => 'Coahuila',
            self::COLIMA => 'Colima',
            self::DURANGO => 'Durango',
            self::ESTADO_DE_MEXICO => 'Estado de México',
            self::GUANAJUATO => 'Guanajuato',
            self::GUERRERO => 'Guerrero',
            self::HIDALGO => 'Hidalgo',
            self::JALISCO => 'Jalisco',
            self::MICHOACAN => 'Michoacán',
            self::MORELOS => 'Morelos',
            self::NAYARIT => 'Nayarit',
            self::NUEVO_LEON => 'Nuevo León',
            self::OAXACA => 'Oaxaca',
            self::PUEBLA => 'Puebla',
            self::QUERETARO => 'Querétaro',
            self::QUINTANA_ROO => 'Quintana Roo',
            self::SAN_LUIS_POTOSI => 'San Luis Potosí',
            self::SINALOA => 'Sinaloa',
            self::SONORA => 'Sonora',
            self::TABASCO => 'Tabasco',
            self::TAMAULIPAS => 'Tamaulipas',
            self::TLAXCALA => 'Tlaxcala',
            self::VERACRUZ => 'Veracruz',
            self::YUCATAN => 'Yucatán',
            self::ZACATECAS => 'Zacatecas',
        };
    }
}
