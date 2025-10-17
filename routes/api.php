<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\GoogleAuthController;

// ========================================
// RUTAS PÚBLICAS (Sin autenticación)
// ========================================

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

// Rutas públicas de usuarios
Route::prefix('users')->group(function () {
    // Registro de usuario
    Route::post('/register', [UserController::class, 'register']);
    
    // Login de usuario
    Route::post('/login', [UserController::class, 'login']);
    
    // Verificación de email (método original con token)
    Route::get('/verifyemail', [UserController::class, 'verifyEmail']);
    
    // Verificación de email con código de 6 dígitos
    Route::post('/verifyemailcode', [UserController::class, 'verifyEmailWithCode']);
    
    // Reset de contraseña (sin autenticación)
    Route::post('/request-password-reset', [UserController::class, 'requestPasswordReset']);
    Route::patch('/reset-password', [UserController::class, 'resetPassword']);
});

// ========================================
// RUTAS DE AUTENTICACIÓN CON GOOGLE
// ========================================

Route::prefix('auth/google')->group(function () {
    // Obtener URL de autenticación de Google
    Route::get('/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
    
    // Callback de Google después de autenticación
    Route::get('/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
    
    // Login con token de Google (para apps móviles/SPA)
    Route::post('/token', [GoogleAuthController::class, 'loginWithGoogleToken']);
});

// ========================================
// RUTAS PROTEGIDAS (Requieren JWT)
// ========================================

// Rutas protegidas de usuarios
Route::prefix('users')->middleware('jwt.auth')->group(function () {
    // Información del usuario autenticado
    Route::get('/me', [UserController::class, 'me']);
    
    // Obtener usuarios (requiere autenticación)
    Route::get('/', [UserController::class, 'getAllUsers']);
    Route::get('/{id}', [UserController::class, 'getUser']);
    
    // Eliminar usuario (requiere autenticación)
    Route::delete('/{id}', [UserController::class, 'deleteUser']);
});

// ========================================
// RUTAS DE QUIZ
// ========================================

// Rutas públicas del quiz
Route::prefix('quiz')->group(function () {
    // Obtener opciones disponibles para el quiz
    Route::get('/options', [QuizController::class, 'getOptions']);
});

// Rutas protegidas del quiz (requieren JWT)
Route::prefix('quiz')->middleware('jwt.auth')->group(function () {
    // Enviar respuestas del quiz
    Route::post('/submit', [QuizController::class, 'submit']);
    
    // Obtener quiz del usuario
    Route::get('/my-quiz', [QuizController::class, 'getUserQuizResponse']);
});

