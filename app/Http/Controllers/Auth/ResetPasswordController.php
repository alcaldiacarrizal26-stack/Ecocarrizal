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
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function create(Request $request, $token)
    {
        $correo = $request->query('correo');

        // 1. Buscar si existe el registro del token para este correo
        // (La tabla de Laravel usa 'email' como nombre de columna por defecto)
        $record = DB::table('password_reset_tokens')
            ->where('email', $correo) 
            ->first();

        // 2. Si no existe el registro o no coincide el hash/token
        if (!$record || !Hash::check($token, $record->token)) {
            return response()->view('auth.token-expirado', [
                'motivo' => 'El enlace de recuperación es inválido o ya ha sido utilizado.'
            ], 403);
        }

        // 3. Verificar si el token expiró (60 minutos configurados)
        $expireInMinutes = config('auth.passwords.users.expire', 60);
        if (Carbon::parse($record->created_at)->addMinutes($expireInMinutes)->isPast()) {
            return response()->view('auth.token-expirado', [
                'motivo' => 'El tiempo de validez de este enlace (60 minutos) ha caducado.'
            ], 403);
        }

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

        // IMPORTANTE: Password::reset espera la llave 'email' por defecto.
        // Aunque internamente nuestro modelo User mapea 'correo' a email, 
        // el array debe llevar la llave 'email'.
        $status = Password::reset(
            [
                'email'    => $request->correo,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token'    => $request->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                // Si el correo aún no está verificado (como ocurre con los nuevos inspectores),
                // lo marcamos como verificado automáticamente al configurar su contraseña.
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                }

                $user->save();

                // BITÁCORA: Nivel en UPPERCASE
                BitacoraEvento::registrar([
                    'modulo'      => 'auth',
                    'tipo_evento' => 'CAMBIO_CONTRASENA',
                    'nivel'       => 'WARNING',
                    'descripcion' => "El usuario {$user->correo} configuró/restableció su contraseña exitosamente.",
                ]);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', '¡Tu contraseña ha sido configurada exitosamente! Ya puedes iniciar sesión con tus credenciales.')
            : back()->withErrors(['correo' => ['No se pudo completar el restablecimiento. El enlace puede haber expirado o los datos son incorrectos.']]);
    }
}