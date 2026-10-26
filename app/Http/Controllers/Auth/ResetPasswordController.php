<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\BitacoraEvento;

class ResetPasswordController extends Controller
{
    public function create(Request $request, $token)
    {
        $correo = $request->query('correo');

        // 1. Buscar si existe el registro del token para este correo
        $record = DB::table('password_reset_tokens')
            ->where('email', $correo) // La tabla por defecto de Laravel guarda el correo en la columna 'email'
            ->first();

        // 2. Si no existe el registro o no coincide el hash/token
        if (!$record || !\Illuminate\Support\Facades\Hash::check($token, $record->token)) {
            return response()->view('auth.token-expirado', [
                'motivo' => 'El enlace de recuperación es inválido o ya ha sido utilizado.'
            ], 403);
        }

        // 3. Verificar si el token expiró (ejemplo: 60 minutos configurados)
        $expireInMinutes = config('auth.passwords.users.expire', 60);
        if (Carbon::parse($record->created_at)->addMinutes($expireInMinutes)->isPast()) {
            return response()->view('auth.token-expirado', [
                'motivo' => 'El tiempo de validez de este enlace (60 minutos) ha caducado.'
            ], 403);
        }

        // Si todo es válido, mostramos el formulario normalmente
        return view('auth.reset-password', [
            'token' => $token,
            'correo' => $correo,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'correo' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $status = Password::reset(
            [
                'correo' => $request->correo,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $request->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
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
            ? redirect()->route('login')->with('success', '¡Tu contraseña ha sido restablecida exitosamente! Ya puedes iniciar sesión con tus nuevas credenciales.')
            : back()->withErrors(['correo' => ['No se pudo completar el restablecimiento. El enlace puede haber expirado o los datos son incorrectos.']]);
    }
}