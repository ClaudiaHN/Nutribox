<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ClienteAuthController extends Controller
{
    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:120'],
            'contrasena' => ['required', 'string'],
        ]);

        $cliente = DB::table('cliente')
            ->where('correo', $datos['correo'])
            ->first();

        if (!$cliente || !Hash::check($datos['contrasena'], $cliente->password_hash)) {
            return back()
                ->withErrors(['correo' => 'El correo o la contraseña son incorrectos.'])
                ->onlyInput('correo');
        }

        $request->session()->regenerate();
        $request->session()->put([
            'id_cliente' => $cliente->id_cliente,
            'nombre_cliente' => $cliente->nombres,
            'correo_cliente' => $cliente->correo,
        ]);

        return redirect()->route('inicio')->with('status', '¡Bienvenido a Nutribox!');
    }

    public function registerForm(): View
    {
        return view('auth.registro');
    }

    public function register(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'telefono' => ['required', 'regex:/^9[0-9]{8}$/'],
            'correo' => ['required', 'email', 'max:120', 'unique:cliente,correo'],
            'distrito' => ['required', 'string', 'max:60'],
            'direccion' => ['required', 'string', 'max:200'],
            'contrasena' => ['required', 'string', 'min:8', 'same:confirmar_contrasena'],
            'confirmar_contrasena' => ['required', 'string'],
        ], [
            'telefono.regex' => 'El teléfono debe empezar con 9 y tener 9 dígitos.',
            'correo.unique' => 'Este correo electrónico ya está registrado.',
            'contrasena.same' => 'Las contraseñas no coinciden.',
        ]);

        DB::table('cliente')->insert([
            'nombres' => $datos['nombres'],
            'telefono' => $datos['telefono'],
            'correo' => $datos['correo'],
            'distrito' => $datos['distrito'],
            'direccion' => $datos['direccion'],
            'password_hash' => Hash::make($datos['contrasena']),
        ]);

        return redirect()->route('login')
            ->with('status', '¡Cuenta creada! Ya puedes iniciar sesión.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inicio');
    }
}
