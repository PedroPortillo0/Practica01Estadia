<?php

namespace App\Domain\Enums\DesafiosDiarios;

enum DailyChallenge: string
{
    case MEDITACION_MATUTINA = 'meditacion_matutina';
    case REFLEXION_NOCTURNA = 'reflexion_nocturna';
    case EJERCICIO_FISICO = 'ejercicio_fisico';
    case LECTURA_ESTOICA = 'lectura_estoica';
    case ACTO_DE_BONDAD = 'acto_de_bondad';
    case TIEMPO_EN_SILENCIO = 'tiempo_en_silencio';
    case PRACTICA_DE_GRATITUD = 'practica_de_gratitud';
    case CONTROL_EMOCIONAL = 'control_emocional';

    public function getLabel(): string
    {
        return match($this) {
            self::MEDITACION_MATUTINA => 'Meditación Matutina',
            self::REFLEXION_NOCTURNA => 'Reflexión Nocturna',
            self::EJERCICIO_FISICO => 'Ejercicio Físico',
            self::LECTURA_ESTOICA => 'Lectura Estoica',
            self::ACTO_DE_BONDAD => 'Acto de Bondad',
            self::TIEMPO_EN_SILENCIO => 'Tiempo en Silencio',
            self::PRACTICA_DE_GRATITUD => 'Práctica de Gratitud',
            self::CONTROL_EMOCIONAL => 'Control Emocional',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::MEDITACION_MATUTINA => '10 minutos cada mañana',
            self::REFLEXION_NOCTURNA => 'Escribir 3 cosas del día',
            self::EJERCICIO_FISICO => '30 minutos de actividad',
            self::LECTURA_ESTOICA => '15 minutos diarios',
            self::ACTO_DE_BONDAD => 'Una buena acción diaria',
            self::TIEMPO_EN_SILENCIO => '20 minutos sin dispositivos',
            self::PRACTICA_DE_GRATITUD => 'Agradecer 5 cosas diarias',
            self::CONTROL_EMOCIONAL => 'Pausar antes de reaccionar',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::MEDITACION_MATUTINA => 'lotus',
            self::REFLEXION_NOCTURNA => 'book',
            self::EJERCICIO_FISICO => 'dumbbell',
            self::LECTURA_ESTOICA => 'books',
            self::ACTO_DE_BONDAD => 'heart',
            self::TIEMPO_EN_SILENCIO => 'phone-slash',
            self::PRACTICA_DE_GRATITUD => 'star',
            self::CONTROL_EMOCIONAL => 'brain',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::MEDITACION_MATUTINA => 'purple',
            self::REFLEXION_NOCTURNA => 'blue',
            self::EJERCICIO_FISICO => 'green',
            self::LECTURA_ESTOICA => 'indigo',
            self::ACTO_DE_BONDAD => 'red',
            self::TIEMPO_EN_SILENCIO => 'teal',
            self::PRACTICA_DE_GRATITUD => 'yellow',
            self::CONTROL_EMOCIONAL => 'purple',
        };
    }
}
