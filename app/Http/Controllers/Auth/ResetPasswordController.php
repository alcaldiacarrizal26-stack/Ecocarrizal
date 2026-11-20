<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BitacoraEvento;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function create(Request $request, $token)
    {
        $correo = $request->query('correo');

        // 1. Buscar si existe el registro del token para este correo
        $record = DB::table('password_reset_tokens')
            ->where('email', $correo)
            ->first();

        // 2. Si no existe el registro o no coincide el hash/token
        if (! $record || ! Hash::check($token, $record->token)) {
            return response()->view('auth.token-expirado', [
                'motivo' => 'El enlace de recuperación es inválido o ya ha sido utilizado.',
            ], 403);
        }

        // 3. Verificar si el token expiró
        $expireInMinutes = config('auth.passwords.users.expire', 60);
        if (Carbon::parse($record->created_at)->addMinutes($expireInMinutes)->isPast()) {
            return response()->view('auth.token-expirado', [
                'motivo' => 'El tiempo de validez de este enlace (60 minutos) ha caducado.',
            ], 403);
        }

        return view('auth.reset-password', [
            'token'  => $token,
            'correo' => $correo,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'correo'   => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $user = User::where('correo', $request->correo)->first();

        if (! $user) {
            return back()->withErrors([
                'correo' => ['No se encontró ningún usuario asociado a este correo electrónico.'],
            ]);
        }

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->first();

        $expireInMinutes = config('auth.passwords.users.expire', 60);
        $tokenValido = $record
            && Hash::check($request->token, $record->token)
            && ! Carbon::parse($record->created_at)->addMinutes($expireInMinutes)->isPast();

        if (! $tokenValido) {
            return back()->withErrors([
                'correo' => ['No se pudo completar el restablecimiento. El enlace puede haber expirado o los datos son incorrectos.'],
            ]);
        }

        // Evitamos doble hash si el modelo User tiene cast 'password' => 'hashed'
        $casts = $user->getCasts();
        $passwordValue = (($casts['password'] ?? null) === 'hashed')
            ? $request->password
            : Hash::make($request->password);

        $user->forceFill([
            'password' => $passwordValue,
        ])->setRememberToken(Str::random(60));

        if (is_null($user->email_verified_at)) {
            $user->email_verified_at = now();
        }

        $user->save();

        DB::table('password_reset_tokens')
            ->where('email', $request->correo)
            ->delete();

        BitacoraEvento::registrar([
            'modulo'      => 'auth',
            'tipo_evento' => 'CAMBIO_CONTRASENA',
            'nivel'       => 'WARNING',
            'descripcion' => "El usuario {$user->correo} configuró/restableció su contraseña exitosamente.",
        ]);

        return redirect()->route('login')
            ->with('success', '¡Tu contraseña ha sido configurada exitosamente! Ya puedes iniciar sesión con tus credenciales.');
    }
}