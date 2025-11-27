<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Application\UseCases\GenerateDailyQuotesWithAI;
use Exception;

class GenerateDailyQuotesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:generate-quotes {--year= : Año para generar las frases (por defecto año actual)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera 365 o 366 frases diarias usando IA y las guarda en la base de datos';

    /**
     * Execute the console command.
     */
    public function handle(GenerateDailyQuotesWithAI $generateQuotesUseCase)
    {
        $this->info('🚀 Iniciando generación de frases diarias con IA...');
        $this->newLine();

        try {
            $year = $this->option('year') ? (int) $this->option('year') : null;

            if ($year !== null) {
                $this->info("📅 Generando frases para el año: {$year}");
            } else {
                $this->info("📅 Generando frases para el año actual");
            }

            $this->newLine();
            $this->info('⏳ Esto puede tomar varios minutos...');
            $this->newLine();

            // Ejecutar el caso de uso
            $result = $generateQuotesUseCase->execute($year);

            // Mostrar resultados
            $this->newLine();
            $this->info('✅ Generación completada!');
            $this->newLine();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total de días', $result['total_days']],
                    ['Frases guardadas', $result['saved']],
                    ['Errores', $result['errors']],
                    ['Año', $result['year']],
                    ['Año bisiesto', $result['is_leap_year'] ? 'Sí' : 'No'],
                ]
            );

            if ($result['errors'] > 0) {
                $this->warn("⚠️  Se encontraron {$result['errors']} errores durante la generación.");
            }

            return Command::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->newLine();
            $this->error('Detalles: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}

