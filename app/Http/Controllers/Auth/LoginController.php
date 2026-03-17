<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Redireccionar después del login.
     *
     * @var string
     */

     protected $redirectTo = '/home';

    protected function authenticated(Request $request, $user)
    {
        // Redirigir al usuario a /home después de un inicio de sesión exitoso
        return redirect('/home');
    }

    /**
     * Crear una nueva instancia del controlador.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Personalizar la respuesta en caso de fallo en el login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()->withErrors([
            'email' => 'Las credenciales no coinciden contactate con el administrador.',
        ])->onlyInput('email');
    }
    
}
