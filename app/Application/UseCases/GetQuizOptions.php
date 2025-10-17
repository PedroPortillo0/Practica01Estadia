<?php

namespace App\Application\UseCases;

use App\Domain\Enums\DatosPersonales\AgeRange;
use App\Domain\Enums\DatosPersonales\Gender;
use App\Domain\Enums\DatosPersonales\SexualOrientation;
use App\Domain\Enums\DatosPersonales\MexicanState;
use App\Domain\Enums\Espiritualidad\ReligiousBelief;
use App\Domain\Enums\Espiritualidad\SpiritualPracticeLevel;
use App\Domain\Enums\Espiritualidad\SpiritualPracticeFrequency;
use App\Domain\Enums\ValoresEstoicos\StoicValue;
use App\Domain\Enums\FilosofiaDeVida\LifePurpose;
use App\Domain\Enums\FilosofiaDeVida\HappinessSource;
use App\Domain\Enums\FilosofiaDeVida\AdversityResponse;
use App\Domain\Enums\FilosofiaDeVida\LifeDevelopmentArea;

class GetQuizOptions
{
    public function execute(): array
    {
        return [
            'success' => true,
            'data' => [
                'age_ranges' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], AgeRange::cases()),
                
                'genders' => [
                    'main' => Gender::getMainOptions(),
                    'additional' => Gender::getAdditionalOptions()
                ],
                
                'sexual_orientations' => [
                    'main' => SexualOrientation::getMainOptions(),
                    'additional' => SexualOrientation::getAdditionalOptions()
                ],
                
                'mexican_states' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], MexicanState::cases()),
                
                'religious_beliefs' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], ReligiousBelief::cases()),
                
                'spiritual_practice_levels' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], SpiritualPracticeLevel::cases()),
                
                'spiritual_practice_frequencies' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], SpiritualPracticeFrequency::cases()),
                
                'stoic_values' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel(),
                    'description' => $case->getDescription()
                ], StoicValue::cases()),
                
                'life_purposes' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], LifePurpose::cases()),
                
                'happiness_sources' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], HappinessSource::cases()),
                
                'adversity_responses' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], AdversityResponse::cases()),
                
                'life_development_areas' => array_map(fn($case) => [
                    'value' => $case->value,
                    'label' => $case->getLabel()
                ], LifeDevelopmentArea::cases())
            ]
        ];
    }
}
