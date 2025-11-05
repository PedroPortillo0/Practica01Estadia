<?php

namespace App\Http\Controllers;

use App\Application\UseCases\LoginAdmin;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    private LoginAdmin $loginAdmin;

    public function __construct(LoginAdmin $loginAdmin)
    {
        $this->loginAdmin = $loginAdmin;
    }

    /**
     * Muestra el formulario de login
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Procesa el login del administrador
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ], [
                'email.required' => 'El email es requerido',
                'email.email' => 'El email debe tener un formato válido',
                'password.required' => 'La contraseña es requerida'
            ]);

            $result = $this->loginAdmin->execute($validated);

            if (!$result['success']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $result['message']);
            }

            // Guardar token en cookie
            $cookie = cookie('auth_token', $result['token'], 60 * 24); // 24 horas

            return redirect()
                ->route('admin.daily-quotes.index')
                ->withCookie($cookie)
                ->with('success', 'Bienvenido al panel de administración');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al iniciar sesión: ' . $e->getMessage());
        }
    }

    /**
     * Cierra la sesión del administrador
     */
    public function logout()
    {
        $cookie = cookie()->forget('auth_token');
        
        return redirect()
            ->route('admin.login')
            ->withCookie($cookie)
            ->with('success', 'Sesión cerrada exitosamente');
    }
}
