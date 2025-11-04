<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reflection;
use Carbon\Carbon;

class DiarioController extends Controller
{
    /**
     * Guardar o actualizar reflexiones matutina/vespertina para una fecha.
     *
     * Reglas de horario (suposición):
     *  - Mañana: 04:00 - 12:00
     *  - Tarde: 12:00 - 20:00
     *
     * Si se intenta guardar la reflexión matutina fuera del horario de mañana,
     * se devuelve error 422. Lo mismo para la reflexión vespertina.
     */
    public function store(Request $request)
    {
        // El middleware JwtAuthMiddleware inyecta el usuario autenticado en
        // los atributos de la request como 'authenticated_user'. No se usa el
        // facade Auth en este proyecto hexagonal, por eso Auth::user() puede
        // devolver null.
        $user = $request->attributes->get('authenticated_user');
        if (!$user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $data = $request->validate([
            'date' => 'required|date',
            'morning_text' => 'nullable|string|max:1000',
            'evening_text' => 'nullable|string|max:1000',
        ]);

        // Hora actual con posibilidad de tomar la zona horaria del usuario si existe
        $tz = $user->timezone ?? config('app.timezone', 'UTC');
        $now = Carbon::now($tz);
        $computedHour = (int)$now->format('H');

        // Soporte para pruebas: permitir sobreescribir la hora mediante cabecera X-Test-Hour
        // o parámetro query ?test_hour=HH — útil para debug sin cambiar el reloj del servidor.
        $overrideHour = null;
        if ($request->headers->has('X-Test-Hour')) {
            $overrideHour = (int) $request->header('X-Test-Hour');
        } elseif ($request->query('test_hour') !== null) {
            $overrideHour = (int) $request->query('test_hour');
        }

        $hour = $overrideHour ?? $computedHour;

    // Ventanas horarias (ajustadas):
    // - Mañana: 01:00 (inclusive) hasta 12:00 (inclusive)
    // - Tarde: 13:00 (inclusive) hasta 23:00 (inclusive)
    $morning_start = 1;   // 01:00
    $morning_end = 12;    // 12:00 (inclusive)
    $evening_start = 13;  // 13:00 (01:00 PM)
    $evening_end = 24;    // 23:00 (11:00 PM) (inclusive)

        if (!empty($data['morning_text'])) {
            // Aceptamos horas entre morning_start y morning_end, ambos inclusive
            if ($hour < $morning_start || $hour > $morning_end) {
                return response()->json([
                    'message' => "La reflexión matutina sólo puede guardarse en horario de la mañana ({$morning_start}:00-{$morning_end}:00).",
                    'debug' => [
                        'user_timezone' => $tz,
                        'computed_hour' => $computedHour,
                        'effective_hour' => $hour,
                        'override' => $overrideHour !== null,
                    ]
                ], 422);
            }
        }

        if (!empty($data['evening_text'])) {
            // Aceptamos horas entre evening_start y evening_end, ambos inclusive
            if ($hour < $evening_start || $hour > $evening_end) {
                return response()->json([
                    'message' => "La reflexión vespertina sólo puede guardarse en horario de la tarde ({$evening_start}:00-{$evening_end}:00).",
                    'debug' => [
                        'user_timezone' => $tz,
                        'computed_hour' => $computedHour,
                        'effective_hour' => $hour,
                        'override' => $overrideHour !== null,
                    ]
                ], 422);
            }
        }

        // Guardar o actualizar la reflexión del usuario para la fecha indicada
        // El objeto User en Domain\Entities tiene la propiedad id privada,
        // exponemos el id a través de getId().
        $reflection = Reflection::firstOrNew([
            'user_id' => $user->getId(),
            'date' => $data['date'],
        ]);

        if (array_key_exists('morning_text', $data)) {
            $reflection->morning_text = $data['morning_text'];
        }
        if (array_key_exists('evening_text', $data)) {
            $reflection->evening_text = $data['evening_text'];
        }

        $reflection->save();

        return response()->json([
            'message' => 'Reflexión guardada correctamente.',
            'data' => $reflection,
        ], 201);
    }

    /**
     * Obtener las reflexiones del día para el usuario autenticado.
     * Si se proporciona ?date=YYYY-MM-DD se usa esa fecha; de lo contrario se usa la fecha actual
     * en la zona horaria del usuario (si está disponible) o UTC.
     */
    public function show(Request $request)
    {
        $user = $request->attributes->get('authenticated_user');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        // Determinar fecha objetivo
        $tz = $user->timezone ?? config('app.timezone', 'UTC');
        $dateParam = $request->query('date');
        if ($dateParam) {
            try {
                $date = Carbon::parse($dateParam, $tz)->toDateString();
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Formato de fecha inválido. Use YYYY-MM-DD.'], 422);
            }
        } else {
            $date = Carbon::now($tz)->toDateString();
        }

        $reflection = Reflection::where('user_id', $user->getId())
            ->where('date', $date)
            ->first();

        if (! $reflection) {
            // Devolver formato vacío consistente con la UI
            return response()->json([
                'data' => [
                    'user_id' => $user->getId(),
                    'date' => $date,
                    'morning_text' => null,
                    'evening_text' => null,
                ]
            ], 200);
        }

        return response()->json(['data' => $reflection], 200);
    }
}
