<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Ports\TokenServiceInterface;
use App\Domain\Ports\UserRepositoryInterface;

class JwtAuthMiddleware
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
            // Obtener token del header Authorization
            $authHeader = $request->header('Authorization');
            
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token de autorización requerido. Formato: Bearer <token>'
                ], 401);
            }

            // Extraer token (remover "Bearer ")
            $token = substr($authHeader, 7);

            if (empty($token)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token vacío'
                ], 401);
            }

            // Validar token JWT
            try {
                $payload = $this->tokenService->verifyToken($token);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token inválido o expirado: ' . $e->getMessage()
                ], 401);
            }

            // Verificar que el usuario existe
            $user = $this->userRepository->findById($payload['user_id']);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ], 401);
            }

            // Verificar que el email esté verificado
            if (!$user->isEmailVerificado()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email no verificado. Por favor verifica tu email antes de continuar.'
                ], 403);
            }

            // Agregar usuario autenticado al request
            $request->attributes->set('authenticated_user', $user);
            $request->attributes->set('token_payload', $payload);

            return $next($request);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de autenticación: ' . $e->getMessage()
            ], 500);
        }
    }
}
