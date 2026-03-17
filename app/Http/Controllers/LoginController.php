<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cookie;


class LoginController extends Controller
{
    /**
     * Mostrar el formulario de inicio de sesión.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Manejar la autenticación del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validación personalizada con mensajes
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                'regex:/^[\w.\-]+@[\w.\-]+\.[a-zA-Z]{2,6}$/'
            ],
            'password' => [
                'required',
                'regex:/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?.]+$/'
            ],
        ], [
            'email.regex' => 'El formato del correo electrónico no es válido. No se permiten caracteres especiales.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, un número y un carácter especial (@$!%*?&.).',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->onlyInput('email');
        }

        // Validación adicional de caracteres no permitidos
        $email = $request->input('email');
        $password = $request->input('password');

        if (preg_match('/[\'"=;`&|<>\^(){}\[\]]/', $email) || preg_match('/[\'"=;`&|<>\^(){}\[\]]/', $password)) {
            return back()->withErrors([
                'email' => 'El correo electrónico o la contraseña contienen caracteres no permitidos (\' " = ; ` & | < > ^ ( ) { } [ ]).',
            ])->onlyInput('email');
        }
        
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Manejo de cookies para "Recuérdame"
            if ($request->has('remember')) {
                // Guardar en cookies por 30 días (43200 minutos)
                Cookie::queue('remember_me', '1', 43200);
                Cookie::queue('remembered_email', $email, 43200);
                Cookie::queue('remembered_password', $password, 43200);
            } else {
                // Eliminar cookies si no está marcado
                Cookie::queue(Cookie::forget('remember_me'));
                Cookie::queue(Cookie::forget('remembered_email'));
                Cookie::queue(Cookie::forget('remembered_password'));
            }

            return redirect()->intended('app');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    /**
     * Cerrar la sesión del usuario autenticado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
