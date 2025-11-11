<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Ports\TokenServiceInterface;
use App\Domain\Ports\UserRepositoryInterface;

class AdminAuth
{
    private TokenServiceInterface $tokenService;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        TokenServiceInterface $tokenService,
        UserRepositoryInterface $userRepository
    ) {
        $this->tokenService = $tokenService;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Obtener token de la cookie de sesión
            $token = $request->cookie('auth_token');
            
            if (!$token) {
                return redirect()->route('admin.login')
                    ->with('error', 'Debes iniciar sesión para acceder al panel de administración');
            }

            // Validar token JWT
            try {
                $payload = $this->tokenService->verifyToken($token);
            } catch (\Exception $e) {
                return redirect()->route('admin.login')
                    ->with('error', 'Sesión expirada. Por favor inicia sesión nuevamente');
            }

            // Verificar que el usuario existe
            $user = $this->userRepository->findById($payload['user_id']);
            
            if (!$user) {
                return redirect()->route('admin.login')
                    ->with('error', 'Usuario no encontrado');
            }

            // Verificar que el usuario sea administrador
            if (!$user->isAdmin()) {
                return redirect()->route('admin.login')
                    ->with('error', 'No tienes permisos de administrador para acceder a esta sección');
            }

            // Verificar que el email esté verificado
            if (!$user->isEmailVerificado()) {
                return redirect()->route('admin.login')
                    ->with('error', 'Debes verificar tu email antes de acceder al panel de administración');
            }

            // Agregar usuario autenticado al request para uso en controladores
            $request->attributes->set('authenticated_user', $user);
            $request->attributes->set('token_payload', $payload);

            return $next($request);

        } catch (\Exception $e) {
            return redirect()->route('admin.login')
                ->with('error', 'Error de autenticación: ' . $e->getMessage());
        }
    }
}
