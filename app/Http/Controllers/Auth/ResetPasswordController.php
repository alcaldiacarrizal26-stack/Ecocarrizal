<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\BitacoraEvento;

class ResetPasswordController extends Controller
{
    public function create(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token, 'correo' => $request->correo]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'correo' => 'required|email',
            'contraseña' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            [
                'email' => $request->correo,
                'password' => $request->contraseña,
                'password_confirmation' => $request->contraseña_confirmation,
                'token' => $request->token
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                BitacoraEvento::registrar([
                    'modulo' => 'auth',
                    'tipo_evento' => 'CAMBIO_CONTRASENA',
                    'nivel' => 'warning',
                    'descripcion' => "El usuario {$user->correo} restableció su contraseña exitosamente.",
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['correo' => [__($status)]]);
    }
}