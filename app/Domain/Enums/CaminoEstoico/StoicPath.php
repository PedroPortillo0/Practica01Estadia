<?php

namespace App\Domain\Enums\CaminoEstoico;

enum StoicPath: string
{
    case PAZ_INTERIOR = 'paz_interior';
    case AUTOCONTROL = 'autocontrol';
    case SABIDURIA = 'sabiduria';
    case RESILENCIA = 'resilencia';
    case PROPOSITO = 'proposito';
    case EQUILIBRIO = 'equilibrio';

    public function getLabel(): string
    {
        return match($this) {
            self::PAZ_INTERIOR => 'Paz Interior',
            self::AUTOCONTROL => 'Autocontrol',
            self::SABIDURIA => 'Sabiduría',
            self::RESILENCIA => 'Resilencia',
            self::PROPOSITO => 'Propósito',
            self::EQUILIBRIO => 'Equilibrio',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::PAZ_INTERIOR => 'Encontrar calma en el caos diario',
            self::AUTOCONTROL => 'Dominar mis emociones y reacciones',
            self::SABIDURIA => 'Desarrollar perspectiva y entendimiento',
            self::RESILENCIA => 'Ser fuerte ante las adversidades',
            self::PROPOSITO => 'Encontrar significado en mi vida',
            self::EQUILIBRIO => 'Balancear todas las áreas de mi vida',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::PAZ_INTERIOR => '🌱',
            self::AUTOCONTROL => '⚙️',
            self::SABIDURIA => '💡',
            self::RESILENCIA => '🛡️',
            self::PROPOSITO => '🔄',
            self::EQUILIBRIO => '⚖️',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::PAZ_INTERIOR => '#4CAF50',
            self::AUTOCONTROL => '#FF9800',
            self::SABIDURIA => '#2196F3',
            self::RESILENCIA => '#F44336',
            self::PROPOSITO => '#9C27B0',
            self::EQUILIBRIO => '#00BCD4',
        };
    }

    public static function getMainOptions(): array
    {
        return [
            self::PAZ_INTERIOR->value => self::PAZ_INTERIOR->getLabel(),
            self::AUTOCONTROL->value => self::AUTOCONTROL->getLabel(),
            self::SABIDURIA->value => self::SABIDURIA->getLabel(),
            self::RESILENCIA->value => self::RESILENCIA->getLabel(),
            self::PROPOSITO->value => self::PROPOSITO->getLabel(),
            self::EQUILIBRIO->value => self::EQUILIBRIO->getLabel(),
        ];
    }

    public static function getAllOptions(): array
    {
        $options = [];
        foreach (self::cases() as $path) {
            $options[] = [
                'value' => $path->value,
                'label' => $path->getLabel(),
                'description' => $path->getDescription(),
                'icon' => $path->getIcon(),
                'color' => $path->getColor(),
            ];
        }
        return $options;
    }
}
