<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    public function run(): void
    {
        $quotes = [
            [
                'content' => 'La felicidad de tu vida depende de la calidad de tus pensamientos.',
                'author' => 'Marco Aurelio'
            ],
            [
                'content' => 'No es que tengamos poco tiempo, sino que perdemos mucho.',
                'author' => 'Séneca'
            ],
            [
                'content' => 'El hombre es perturbado no por las cosas, sino por la opinión que tiene de ellas.',
                'author' => 'Epicteto'
            ],
            [
                'content' => 'La vida es muy corta para gastarla en rencores.',
                'author' => 'Marco Aurelio'
            ],
            [
                'content' => 'Quien quiera vivir feliz que no se preocupe por lo que no puede controlar.',
                'author' => 'Epicteto'
            ],
            [
                'content' => 'La fortuna solo favorece a la mente preparada.',
                'author' => 'Séneca'
            ],
            [
                'content' => 'Pierde el día aquel que no realiza una buena acción, no ayuda a un amigo o no mejora su carácter.',
                'author' => 'Marco Aurelio'
            ],
            [
                'content' => 'No busques que las cosas ocurran como tú quieres, desea más bien que se produzcan tal como se producen.',
                'author' => 'Epicteto'
            ],
        ];

        foreach ($quotes as $quote) {
            Quote::create($quote);
        }
    }
}