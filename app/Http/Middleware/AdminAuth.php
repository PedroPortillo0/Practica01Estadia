<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // TODO: Implementar autenticación de administrador
        // Por ahora, el panel está abierto para desarrollo
        
        // Ejemplo de implementación futura:
        // if (!auth()->check() || !auth()->user()->is_admin) {
        //     return redirect()->route('login')->with('error', 'Acceso no autorizado');
        // }
        
        return $next($request);
    }
}
