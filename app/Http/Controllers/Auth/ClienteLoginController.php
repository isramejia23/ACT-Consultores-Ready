<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;

class ClienteLoginController extends Controller
{
    /**
     * Muestra el formulario de login para clientes.
     */
    public function showLoginForm()
    {
        return view('auth.cliente-login');
    }

    /**
     * Maneja el intento de autenticación del cliente.
     */
    public function login(Request $request)
    {
        // Validar los datos del formulario
        $request->validate([
            'email_cliente' => 'required|email',
            'password' => 'required',
        ]);

        // Intentar autenticar al cliente
        if (Auth::guard('cliente')->attempt([
            'email_cliente' => $request->email_cliente,
            'password' => $request->password,
        ])) {
            // Redirigir al dashboard de clientes si la autenticación es exitosa
            return redirect()->route('clientes.dashboard');
        }

        // Redirigir de vuelta con un mensaje de error si la autenticación falla
        return back()->withErrors([
            'email_cliente' => 'Credenciales incorrectas, en caso de haberlas olvidado contáctese con su asesor.',
        ]);
    }

    /**
     * Cierra la sesión del cliente.
     */
    public function logout()
    {
        Auth::guard('cliente')->logout();
        return redirect(env('APP_MAIN_DOMAIN'));
    }
}