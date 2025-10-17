<?php

namespace App\Domain\Enums\ValoresEstoicos;

enum StoicValue: string
{
    case SABIDURIA = 'sabiduria';
    case JUSTICIA = 'justicia';
    case FORTALEZA = 'fortaleza';
    case TEMPLANZA = 'templanza';
    case ACEPTACION = 'aceptacion';
    case PRESENTE = 'presente';
    case VIRTUD = 'virtud';
    case RAZON = 'razon';

    public function getLabel(): string
    {
        return match($this) {
            self::SABIDURIA => 'Sabiduría (Sophia)',
            self::JUSTICIA => 'Justicia (Dikaiosyne)',
            self::FORTALEZA => 'Fortaleza (Andreia)',
            self::TEMPLANZA => 'Templaza (Sophrosyne)',
            self::ACEPTACION => 'Aceptacion',
            self::PRESENTE => 'Presente',
            self::VIRTUD => 'Virtud',
            self::RAZON => 'Razon',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::SABIDURIA => 'Búsqueda del conocimiento y comprensión profunda',
            self::JUSTICIA => 'Actuar con integridad y equidad hacia otros',
            self::FORTALEZA => 'Valentía para enfrentar desafíos y adversidades',
            self::TEMPLANZA => 'Autocontrol y moderación en todas las cosas',
            self::ACEPTACION => 'Aceptar lo que no podemos cambiar',
            self::PRESENTE => 'Vivir en el momento presente',
            self::VIRTUD => 'La virtud es el único bien verdadero',
            self::RAZON => 'Usar la razón para guiar nuestras acciones',
        };
    }
}
