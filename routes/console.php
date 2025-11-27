<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ========================================
// TAREAS PROGRAMADAS (Scheduled Tasks)
// ========================================

/**
 * Genera automáticamente las frases del año siguiente el 31 de diciembre
 * Se ejecuta a las 23:59 del 31 de diciembre de cada año
 * 
 * Cron: 59 23 31 12 * (minuto 59, hora 23, día 31, mes 12, cualquier día de la semana)
 */
Schedule::call(function () {
    $nextYear = (int) date('Y') + 1;
    Artisan::call('ai:generate-quotes', ['--year' => $nextYear]);
})->cron('59 23 31 12 *')
    ->timezone('America/Mexico_City') // Ajusta según tu zona horaria
    ->description('Generar frases diarias para el año siguiente')
    ->emailOutputOnFailure(config('mail.from.address')); // Opcional: enviar email si falla
