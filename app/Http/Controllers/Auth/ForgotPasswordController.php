<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\BitacoraEvento;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Buscamos directamente por nuestra columna 'correo' en la base de datos
        $user = User::where('correo', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos ningún usuario registrado con este correo electrónico.']);
        }

        // Generamos el token utilizando el correo real de la BD
        $token = Password::broker()->createToken($user);

        // Aquí puedes simular o enviar la notificación/correo con el enlace de restablecimiento
        // Ejemplo de enlace: url('/reset-password/' . $token . '?correo=' . urlencode($user->correo))

        BitacoraEvento::registrar([
            'modulo' => 'auth',
            'tipo_evento' => 'SOLICITUD_RECUPERACION',
            'nivel' => 'info',
            'descripcion' => "Se solicitó un enlace de recuperación de contraseña para el correo: {$user->correo}",
        ]);

        return back()->with('success', '¡Se han enviado las instrucciones de recuperación a tu correo electrónico!');
    }
}