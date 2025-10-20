<?php

namespace App\Application\UseCases;

use Exception;

class GetDailyQuote
{
    private string $csvPath;

    public function __construct()
    {
        $this->csvPath = storage_path('app/daily_quotes.csv');
    }

    /**
     * Obtiene la frase del día basada en el día del año
     * 
     * @param bool $includeDetail Si debe incluir información completa
     * @return array
     */
    public function execute(bool $includeDetail = false): array
    {
        try {
            // Verificar que el archivo existe
            if (!file_exists($this->csvPath)) {
                throw new Exception('Archivo de frases no encontrado');
            }

            // Leer todas las frases del CSV
            $quotes = $this->readQuotesFromCSV();

            if (empty($quotes)) {
                throw new Exception('No hay frases disponibles');
            }

            // Calcular qué frase mostrar basado en el día del año
            $dayOfYear = (int) date('z'); // 0-364 (0-365 en año bisiesto)
            $totalQuotes = count($quotes);
            
            // Usar módulo para ciclar las frases
            $quoteIndex = $dayOfYear % $totalQuotes;
            
            // Obtener la frase del día
            $dailyQuote = $quotes[$quoteIndex];

            // Preparar respuesta según si se pide detalle o no
            if ($includeDetail) {
                return [
                    'success' => true,
                    'data' => [
                        'id' => $dailyQuote['id'],
                        'quote' => $dailyQuote['quote'],
                        'full_quote' => $dailyQuote['full_quote'],
                        'author' => $dailyQuote['author'],
                        'author_bio' => $dailyQuote['author_bio'],
                        'context' => $dailyQuote['context'],
                        'category' => $dailyQuote['category'],
                        'date' => date('Y-m-d'),
                        'day_of_year' => $dayOfYear + 1
                    ]
                ];
            }

            // Respuesta simple para el dashboard
            return [
                'success' => true,
                'data' => [
                    'id' => $dailyQuote['id'],
                    'quote' => $dailyQuote['quote'],
                    'author' => $dailyQuote['author'],
                    'category' => $dailyQuote['category'],
                    'date' => date('Y-m-d')
                ]
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener la frase del día: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Lee las frases del archivo CSV
     * 
     * @return array
     */
    private function readQuotesFromCSV(): array
    {
        $quotes = [];
        
        if (($handle = fopen($this->csvPath, 'r')) !== false) {
            // Leer la primera línea (encabezados)
            $headers = fgetcsv($handle);
            
            // Leer el resto de líneas
            while (($data = fgetcsv($handle)) !== false) {
                // Ignorar líneas vacías o con datos incompletos
                if (empty($data) || count($data) !== count($headers) || empty($data[0])) {
                    continue;
                }
                
                // Crear array asociativo con los encabezados
                $quote = array_combine($headers, $data);
                $quotes[] = $quote;
            }
            
            fclose($handle);
        }
        
        return $quotes;
    }

    /**
     * Obtiene todas las frases (útil para testing o admin)
     * 
     * @return array
     */
    public function getAllQuotes(): array
    {
        try {
            if (!file_exists($this->csvPath)) {
                throw new Exception('Archivo de frases no encontrado');
            }

            $quotes = $this->readQuotesFromCSV();

            return [
                'success' => true,
                'data' => $quotes,
                'total' => count($quotes)
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener las frases: ' . $e->getMessage()
            ];
        }
    }
}

