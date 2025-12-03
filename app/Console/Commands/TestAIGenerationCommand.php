<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Ports\AIServiceInterface;
use Exception;

class TestAIGenerationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:test {--count=3 : Número de frases a generar para la prueba}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba el motor de IA generando unas pocas frases';

    /**
     * Execute the console command.
     */
    public function handle(AIServiceInterface $aiService)
    {
        $this->info('🧪 Iniciando prueba del motor de IA...');
        $this->newLine();

        try {
            $count = (int) $this->option('count');
            
            if ($count < 1 || $count > 10) {
                $this->warn('El número de frases debe estar entre 1 y 10. Usando 3 por defecto.');
                $count = 3;
            }

            $this->info("📝 Generando {$count} frases de prueba...");
            $this->newLine();

            // Crear un prompt de prueba
            $prompt = "Genera exactamente {$count} frases estoicas diarias únicas e inspiradoras. 

Cada frase debe seguir este formato JSON estricto:
{
  \"quotes\": [
    {
      \"quote\": \"Texto de la frase estoica\",
      \"author\": \"Nombre del filósofo estoico (ej: Marco Aurelio, Séneca, Epicteto, etc.)\",
      \"category\": \"Categoría (ej: Sabiduría, Resiliencia, Virtud, Autocontrol, Aceptación, etc.)\"
    }
  ]
}

Requisitos:
- Todas las frases deben ser únicas y diferentes
- Deben ser frases estoicas auténticas o inspiradas en filosofía estoica
- Los autores deben ser filósofos estoicos reconocidos
- Las categorías deben variar
- Responde SOLO con el JSON, sin texto adicional antes o después
- Genera exactamente {$count} frases";

            $this->info('⏳ Enviando petición a la API de IA...');
            
            // Generar el texto
            $generatedText = $aiService->generateText($prompt, [
                'temperature' => 0.9,
                'max_tokens' => 2000,
            ]);

            $this->info('✅ Respuesta recibida de la IA');
            $this->newLine();

            // Intentar parsear el JSON
            $this->info('📋 Contenido generado:');
            $this->line('─' . str_repeat('─', 70));
            $this->line($generatedText);
            $this->line('─' . str_repeat('─', 70));
            $this->newLine();

            // Intentar extraer JSON
            $jsonMatch = [];
            if (preg_match('/\{[\s\S]*\}/', $generatedText, $jsonMatch)) {
                $json = json_decode($jsonMatch[0], true);
                
                if (isset($json['quotes']) && is_array($json['quotes'])) {
                    $this->info('✅ JSON parseado correctamente');
                    $this->newLine();
                    
                    $this->table(
                        ['#', 'Frase', 'Autor', 'Categoría'],
                        array_map(function($quote, $index) {
                            return [
                                $index + 1,
                                substr($quote['quote'] ?? '', 0, 50) . '...',
                                $quote['author'] ?? 'N/A',
                                $quote['category'] ?? 'N/A'
                            ];
                        }, $json['quotes'], array_keys($json['quotes']))
                    );

                    $this->newLine();
                    $this->info('✅ Prueba completada exitosamente!');
                    $this->info("Se generaron " . count($json['quotes']) . " frases correctamente.");
                    
                    return Command::SUCCESS;
                } else {
                    $this->warn('⚠️  El JSON no contiene el formato esperado (quotes array)');
                }
            } else {
                $this->warn('⚠️  No se pudo encontrar JSON en la respuesta');
            }

            $this->newLine();
            $this->info('ℹ️  La conexión con la IA funciona, pero el formato de respuesta puede necesitar ajustes.');
            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            
            if ($this->option('verbose')) {
                $this->error('Detalles: ' . $e->getTraceAsString());
            } else {
                $this->warn('💡 Ejecuta con --verbose para ver más detalles');
            }
            
            return Command::FAILURE;
        }
    }
}

