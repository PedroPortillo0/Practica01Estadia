<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DailyQuote;
use Illuminate\Support\Facades\DB;

class DailyQuotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // NOTA: El archivo CSV ya no existe. Las frases iniciales ya fueron importadas.
        // Ahora todas las frases se gestionan desde el panel web de administración.
        // Este seeder solo se ejecutó una vez para la carga inicial.
        
        $this->command->info('ℹ️  Las frases se gestionan desde el panel web: /admin/daily-quotes');
        $this->command->info('ℹ️  Total de frases en la base de datos: ' . DailyQuote::count());
        
        // Si quieres agregar frases de demostración, puedes descomentar esto:
        /*
        DailyQuote::create([
            'quote' => 'Tu frase aquí',
            'author' => 'Autor',
            'category' => 'Motivacional',
            'day_of_year' => 1,
            'is_active' => true
        ]);
        */
    }
}
