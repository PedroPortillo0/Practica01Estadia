<?php

namespace App\Domain\Ports;

interface AIServiceInterface
{
    /**
     * Genera contenido de texto usando IA
     * 
     * @param string $prompt El prompt para la IA
     * @param array $options Opciones adicionales (temperatura, max_tokens, etc.)
     * @return string El contenido generado
     * @throws \Exception Si hay error en la generación
     */
    public function generateText(string $prompt, array $options = []): string;

    /**
     * Genera múltiples textos en batch
     * 
     * @param array $prompts Array de prompts
     * @param array $options Opciones adicionales
     * @return array Array de textos generados
     * @throws \Exception Si hay error en la generación
     */
    public function generateBatch(array $prompts, array $options = []): array;
}

