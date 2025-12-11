<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reflection;
use Carbon\Carbon;

class DiarioController extends Controller
{
    /**
     * Guardar o actualizar una sola reflexión para la fecha actual.
     * - La fecha se calcula automáticamente (no se acepta date desde el cliente).
     * - No hay restricción horaria: el usuario puede escribir a cualquier hora.
     * - Se registra la hora en created_at/updated_at.
     * - La fecha se calcula automáticamente (no se acepta `date` desde el cliente).
     * - No hay restricción horaria: el usuario puede escribir a cualquier hora.
     * - Se registra la hora en `created_at`/`updated_at`.
     */
    public function store(Request $request)
    {
        $user = $request->attributes->get('authenticated_user');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $data = $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        $tz = $user->timezone ?? config('app.timezone', 'UTC');
        $now = Carbon::now($tz);
        $date = $now->toDateString();

        // Guardar o actualizar la reflexión del usuario para la fecha actual
        $reflection = Reflection::firstOrNew([
            'user_id' => $user->getId(),
            'date' => $date,
        ]);

        $isNew = !$reflection->exists;
        $isNew = ! $reflection->exists;
        $reflection->text = $data['text'];
        $reflection->save();

        // Devolver la hora registrada (created_at) en la zona del usuario
        $time = $reflection->created_at
            ? $reflection->created_at->setTimezone($tz)->format('H:i:s')
            : $now->format('H:i:s');

        return response()->json([
            'message' => $isNew ? 'Reflexión creada correctamente.' : 'Reflexión actualizada correctamente.',
            'data' => [
                'id' => $reflection->id,
                'user_id' => $reflection->user_id,
                'date' => $reflection->date,
                'text' => $reflection->text,
                'time' => $time,
            ]
        ], $isNew ? 201 : 200);
    }

    /**
     * Obtener las reflexiones del día para el usuario autenticado.
     * Si se proporciona ?date=YYYY-MM-DD se usa esa fecha; de lo contrario se usa la fecha actual
     * en la zona horaria del usuario (si está disponible) o UTC.
     */
    /**
     * Mostrar reflexiones:
     * - Si ?all=1 → devuelve todas las reflexiones del usuario
     * - Si no → devuelve la reflexión del día actual (fecha automática)
     */
    public function show(Request $request)
    {
        $user = $request->attributes->get('authenticated_user');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $tz = $user->timezone ?? config('app.timezone', 'UTC');

        if ($request->query('all')) {
            $all = Reflection::where('user_id', $user->getId())
                ->orderBy('date', 'desc')
                ->get()
                ->map(function($r) use ($tz) {
                    return [
                        'id' => $r->id,
                        'date' => $r->date,
                        'text' => $r->text,
                        'time' => $r->created_at ? $r->created_at->setTimezone($tz)->format('H:i:s') : null,
                    ];
                });

            return response()->json(['data' => $all], 200);
        }

        $date = Carbon::now($tz)->toDateString();

        $reflection = Reflection::where('user_id', $user->getId())
            ->where('date', $date)
            ->first();

        if (! $reflection) {
            return response()->json([
                'data' => [
                    'user_id' => $user->getId(),
                    'date' => $date,
                    'text' => null,
                ]
            ], 200);
        }

        return response()->json([
            'data' => [
                'id' => $reflection->id,
                'date' => $reflection->date,
                'text' => $reflection->text,
                'time' => $reflection->created_at ? $reflection->created_at->setTimezone($tz)->format('H:i:s') : null,
            ]
        ], 200);
    }

    /**
     * Devuelve todas las reflexiones del usuario (endpoint explícito para Postman)
     */
    public function all(Request $request)
    {
        $user = $request->attributes->get('authenticated_user');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $tz = $user->timezone ?? config('app.timezone', 'UTC');

        $all = Reflection::where('user_id', $user->getId())
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($r) use ($tz) {
                return [
                    'id' => $r->id,
                    'date' => $r->date,
                    'text' => $r->text,
                    'time' => $r->created_at ? $r->created_at->setTimezone($tz)->format('H:i:s') : null,
                ];
            });

        return response()->json(['data' => $all], 200);
    }

    /**
     * Actualizar una reflexión existente (solo del usuario autenticado).
     * Acepta text. Devuelve 404 si no existe o no pertenece al usuario.
     */
    public function update(Request $request, $id)
    {
        $user = $request->attributes->get('authenticated_user');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }
        $data = $request->validate([
            'text' => 'required|string|max:2000',
        ]);

        $reflection = Reflection::where('id', $id)->where('user_id', $user->getId())->first();

        if (! $reflection) {
            return response()->json(['message' => 'Reflexión no encontrada.'], 404);
        }

        $reflection->text = $data['text'];
        $reflection->save();

        return response()->json(['message' => 'Reflexión actualizada correctamente.', 'data' => $reflection], 200);
    }

    /**
     * Eliminar una reflexión del usuario autenticado.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->attributes->get('authenticated_user');
        if (! $user) {
            return response()->json(['message' => 'No autenticado.'], 401);
        }

        $reflection = Reflection::where('id', $id)->where('user_id', $user->getId())->first();

        if (! $reflection) {
            return response()->json(['message' => 'Reflexión no encontrada.'], 404);
        }

        $reflection->delete();

        return response()->json(['message' => 'Reflexión eliminada correctamente.'], 200);
    }
}
