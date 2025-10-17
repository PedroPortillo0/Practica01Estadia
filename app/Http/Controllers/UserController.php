<?php

namespace App\Http\Controllers;

use App\Application\UseCases\RegisterUser;
use App\Application\UseCases\LoginUser;
use App\Application\UseCases\VerifyEmail;
use App\Application\UseCases\VerifyEmailWithCode;
use App\Application\UseCases\RequestPasswordReset;
use App\Application\UseCases\ResetPassword;
use App\Application\UseCases\GetUserById;
use App\Application\UseCases\GetAllUsers;
use App\Application\UseCases\DeleteUser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct(
        private RegisterUser $registerUser,
        private LoginUser $loginUser,
        private VerifyEmail $verifyEmail,
        private VerifyEmailWithCode $verifyEmailWithCode,
        private RequestPasswordReset $requestPasswordReset,
        private ResetPassword $resetPassword,
        private GetUserById $getUserById,
        private GetAllUsers $getAllUsers,
        private DeleteUser $deleteUser
        ) {}

        /**
         * Obtener usuario autenticado desde el middleware
         */
        private function getAuthenticatedUser(Request $request)
        {
            return $request->attributes->get('authenticated_user');
        }

        /**
         * Obtener payload del token desde el middleware
         */
        private function getTokenPayload(Request $request): ?array
        {
            return $request->attributes->get('token_payload');
        }

        public function register(Request $request): JsonResponse
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|min:2|max:255',
            'apellidos' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'confirm_password' => 'required|string|min:6|same:password'
        ], [
            'nombre.required' => 'El nombre es requerido',
            'nombre.min' => 'El nombre debe tener al menos 2 caracteres',
            'apellidos.required' => 'Los apellidos son requeridos',
            'apellidos.min' => 'Los apellidos deben tener al menos 2 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe tener un formato válido',
            'password.required' => 'La contraseña es requerida',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'confirm_password.required' => 'La confirmación de contraseña es requerida',
            'confirm_password.min' => 'La confirmación de contraseña debe tener al menos 6 caracteres',
            'confirm_password.same' => 'Las contraseñas no coinciden'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->registerUser->execute($request->all());

        return response()->json($result, $result['success'] ? 201 : 400);
    }

    public function login(Request $request): JsonResponse
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ], [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe tener un formato válido',
            'password.required' => 'La contraseña es requerida'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->loginUser->execute($request->only(['email', 'password']));

        return response()->json($result, $result['success'] ? 200 : 401);
    }

    public function verifyEmail(Request $request): JsonResponse
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token de verificación requerido'
            ], 400);
        }

        $result = $this->verifyEmail->execute($token);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function verifyEmailWithCode(Request $request): JsonResponse
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
            'code' => 'required|string|size:6'
        ], [
            'user_id.required' => 'El ID de usuario es requerido',
            'code.required' => 'El código de verificación es requerido',
            'code.size' => 'El código debe tener exactamente 6 dígitos'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->verifyEmailWithCode->execute(
            $request->input('user_id'),
            $request->input('code')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function requestPasswordReset(Request $request): JsonResponse
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ], [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe tener un formato válido'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->requestPasswordReset->execute($request->input('email'));

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        // Validación de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
            'new_password' => 'required|string|min:6'
        ], [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe tener un formato válido',
            'code.required' => 'El código de verificación es requerido',
            'code.size' => 'El código debe tener exactamente 6 dígitos',
            'new_password.required' => 'La nueva contraseña es requerida',
            'new_password.min' => 'La nueva contraseña debe tener al menos 6 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 400);
        }

        $result = $this->resetPassword->execute(
            $request->input('email'),
            $request->input('code'),
            $request->input('new_password')
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function getUser(string $id): JsonResponse
    {
        $result = $this->getUserById->execute($id);
        
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function getAllUsers(Request $request): JsonResponse
    {
        // Obtener parámetros de paginación de la query string
        $page = (int) $request->query('page', 1);
        $limit = (int) $request->query('limit', 10);

        $result = $this->getAllUsers->execute($page, $limit);
        
        return response()->json($result, $result['success'] ? 200 : 400);
    }

        public function deleteUser(string $id): JsonResponse
        {
            $result = $this->deleteUser->execute($id);
            
            return response()->json($result, $result['success'] ? 200 : 404);
        }

        /**
         * Obtener información del usuario autenticado
         */
        public function me(Request $request): JsonResponse
        {
            $user = $this->getAuthenticatedUser($request);
            $payload = $this->getTokenPayload($request);
            
            return response()->json([
                'success' => true,
                'message' => 'Usuario autenticado obtenido exitosamente',
                'data' => [
                    'user' => $user->toArray(),
                    'token_info' => [
                        'user_id' => $payload['user_id'] ?? null,
                        'expires_at' => $payload['exp'] ?? null,
                        'issued_at' => $payload['iat'] ?? null
                    ]
                ]
            ], 200);
        }
}
