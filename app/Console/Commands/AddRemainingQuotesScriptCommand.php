<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Domain\Ports\DailyQuoteRepositoryInterface;
use App\Domain\Entities\DailyQuote;
use Exception;
use Illuminate\Support\Facades\Log;

class AddRemainingQuotesScriptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:add-script';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrega frases predefinidas para los días restantes del año (incluyendo hoy)';

    /**
     * Execute the console command.
     */
    public function handle(DailyQuoteRepositoryInterface $quoteRepository)
    {
        $this->info('🚀 Agregando frases predefinidas para los días restantes del año...');
        $this->newLine();

        // Calcular el día actual del año (1-366)
        $currentDayOfYear = (int) date('z') + 1;
        
        // Calcular el total de días del año
        $year = (int) date('Y');
        $isLeapYear = (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0));
        $totalDays = $isLeapYear ? 366 : 365;
        
        // Calcular días restantes (incluyendo hoy)
        $remainingDays = $totalDays - $currentDayOfYear + 1;
        
        $this->info("📅 Año: {$year} (" . ($isLeapYear ? 'Bisiesto' : 'No bisiesto') . ")");
        $this->info("📆 Día actual del año: {$currentDayOfYear}");
        $this->info("📆 Total de días del año: {$totalDays}");
        $this->info("📝 Días restantes (incluyendo hoy): {$remainingDays}");
        $this->newLine();

        // Frases predefinidas para los días restantes
        $quotes = [
            [
                'quote' => 'El tiempo es el recurso más valioso que tenemos. Úsalo sabiamente, inviértelo en lo que realmente importa y no lo desperdicies en preocupaciones vanas.',
                'author' => 'Marco Aurelio',
                'category' => 'Sabiduría'
            ],
            [
                'quote' => 'Acepta lo que no puedes cambiar y enfoca tu energía en lo que sí puedes controlar. La paz interior viene de esta distinción.',
                'author' => 'Epicteto',
                'category' => 'Aceptación'
            ],
            [
                'quote' => 'Cada día es una nueva oportunidad para ser mejor que ayer. No esperes al año nuevo para cambiar; comienza ahora mismo.',
                'author' => 'Séneca',
                'category' => 'Perseverancia'
            ],
            [
                'quote' => 'La verdadera felicidad no depende de las circunstancias externas, sino de tu actitud y tu capacidad para encontrar significado en cada momento.',
                'author' => 'Marco Aurelio',
                'category' => 'Virtud'
            ],
            [
                'quote' => 'Reflexiona sobre tus acciones del día. ¿Qué hiciste bien? ¿Qué podrías mejorar? El autoconocimiento es el primer paso hacia la sabiduría.',
                'author' => 'Séneca',
                'category' => 'Autocontrol'
            ],
            [
                'quote' => 'No temas el final del año, sino el desperdicio de los días que te quedan. Cada momento es precioso y único.',
                'author' => 'Marco Aurelio',
                'category' => 'Resiliencia'
            ],
            [
                'quote' => 'La adversidad revela tu verdadero carácter. En los momentos difíciles, mantén la calma y actúa con virtud.',
                'author' => 'Epicteto',
                'category' => 'Virtud'
            ],
            [
                'quote' => 'Agradece por lo que tienes hoy, no te preocupes por lo que falta. La gratitud transforma lo que tenemos en suficiente.',
                'author' => 'Séneca',
                'category' => 'Aceptación'
            ],
            [
                'quote' => 'El año que termina no define el que viene. Cada día es una página en blanco donde puedes escribir tu historia.',
                'author' => 'Marco Aurelio',
                'category' => 'Perseverancia'
            ],
            [
                'quote' => 'Termina el año con sabiduría: aprende del pasado, vive el presente y prepárate para el futuro con serenidad y propósito.',
                'author' => 'Séneca',
                'category' => 'Sabiduría'
            ]
        ];

        // Verificar que tenemos suficientes frases
        if (count($quotes) < $remainingDays) {
            $this->warn("⚠️  ADVERTENCIA: Solo hay " . count($quotes) . " frases definidas, pero se necesitan {$remainingDays}.");
            $this->warn("   Se usarán las frases disponibles y se repetirán si es necesario.");
            $this->newLine();
        }

        $saved = 0;
        $skipped = 0;
        $errors = 0;

        $this->info('🚀 Iniciando guardado de frases...');
        $this->newLine();

        // Guardar las frases
        for ($i = 0; $i < $remainingDays; $i++) {
            $dayOfYear = $currentDayOfYear + $i;
            
            // Usar la frase correspondiente (repetir si es necesario)
            $quoteIndex = $i % count($quotes);
            $quoteData = $quotes[$quoteIndex];
            
            try {
                // Verificar si ya existe una frase para este día
                $existing = $quoteRepository->findByDayOfYear($dayOfYear);
                if ($existing) {
                    $this->warn("  ⏭️  Día {$dayOfYear}: Ya existe, omitiendo");
                    $skipped++;
                    continue;
                }
                
                // Crear entidad de dominio
                $quote = new DailyQuote(
                    $quoteData['quote'],
                    $quoteData['author'],
                    $quoteData['category'],
                    $dayOfYear,
                    true // is_active
                );
                
                // Guardar
                $savedQuote = $quoteRepository->save($quote);
                $saved++;
                
                $quotePreview = substr($quoteData['quote'], 0, 60);
                $this->info("  ✅ Día {$dayOfYear}: Guardada - \"{$quotePreview}...\"");
                
                Log::info("Frase guardada para el día {$dayOfYear} desde comando");
                
            } catch (Exception $e) {
                $errors++;
                $this->error("  ❌ Día {$dayOfYear}: Error - " . $e->getMessage());
                Log::error("Error guardando frase para el día {$dayOfYear}: " . $e->getMessage());
            }
        }

        // Mostrar resumen
        $this->newLine();
        $this->info('✅ Proceso completado!');
        $this->newLine();
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Frases guardadas', $saved],
                ['Omitidas (ya existían)', $skipped],
                ['Errores', $errors],
                ['Días cubiertos', "{$currentDayOfYear} a " . ($currentDayOfYear + $remainingDays - 1)],
            ]
        );

        if ($errors > 0) {
            $this->warn("⚠️  Se encontraron {$errors} errores durante el proceso.");
            return Command::FAILURE;
        }

        $this->info("✨ ¡Todas las frases se guardaron exitosamente!");
        return Command::SUCCESS;
    }
}

