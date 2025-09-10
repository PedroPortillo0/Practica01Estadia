<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;



// Rutas de usuarios
Route::prefix('users')->group(function () {
    // Registro de usuario
    Route::post('/register', [UserController::class, 'register']);
    
    // Login de usuario
    Route::post('/login', [UserController::class, 'login']);
    
    // Verificación de email (método original con token)
    Route::get('/verifyemail', [UserController::class, 'verifyEmail']);
    
    // Verificación de email con código de 6 dígitos
    Route::post('/verifyemailcode', [UserController::class, 'verifyEmailWithCode']);
    
    // Cambio de contraseña
    Route::post('/request-password-reset', [UserController::class, 'requestPasswordReset']);
    Route::patch('/reset-password', [UserController::class, 'resetPassword']);
    
    // Obtener usuarios
    Route::get('/', [UserController::class, 'getAllUsers']);


     Route::get('/{id}', [UserController::class, 'getUser']);
});

// Ruta de prueba
Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
        'timestamp' => now()
    ]);
});

