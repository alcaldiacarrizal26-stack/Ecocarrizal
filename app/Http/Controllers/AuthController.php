<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Direccion;
use App\Models\BitacoraEvento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Muestra la vista principal.
     */
    public function showHome()
    {
        return view('welcome');
    }

    /**
     * Muestra la vista dedicada de inicio de sesión.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Muestra la vista dedicada de registro.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesa el inicio de sesión del usuario.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo'     => ['required', 'string', 'email'],
            'contraseña' => ['required', 'string'],
        ]);

        $attemptData = [
            'correo'   => $credentials['correo'],
            'password' => $credentials['contraseña'],
        ];

        if (Auth::attempt($attemptData, $request->boolean('remember'))) {
            $user = Auth::user();

            // RESTRICCIÓN DE SEGURIDAD: Validar si el correo ha sido verificado
            if (is_null($user->email_verified_at)) {
                Auth::logout(); // Cierra la sesión inmediatamente
                
                return back()->withErrors([
                    'correo' => 'Tu cuenta aún no ha sido verificada. Por favor, revisa tu bandeja de correo electrónico para activar tu cuenta antes de iniciar sesión.',
                ])->onlyInput('correo');
            }

            $request->session()->regenerate();

            // Registro en la Bitácora de Auditoría para el Login exitoso
            BitacoraEvento::registrar([
                'modulo'      => 'auth',
                'tipo_evento' => 'LOGIN',
                'nivel'       => 'info',
                'descripcion' => "El usuario {$user->nombre} {$user->apellido} ha iniciado sesión en el sistema.",
                'model'       => $user,
            ]);

            // Redirige al controlador central (DashboardController) que despacha la vista según rol
            return redirect()->intended('/dashboard')->with('success', '¡Bienvenido de nuevo, ' . $user->nombre . '!');
        }

        // Registro de intento fallido en bitácora por seguridad
        BitacoraEvento::registrar([
            'modulo'      => 'auth',
            'tipo_evento' => 'LOGIN_FALLIDO',
            'nivel'       => 'warning',
            'descripcion' => "Intento fallido de inicio de sesión para el correo: {$credentials['correo']}.",
        ]);

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    /**
     * Procesa el registro de nuevos usuarios o empresas (Ciudadano).
     */
    public function register(Request $request)
    {
        $request->validate([
            'nacionalidad'     => 'required|in:V,E,J,G',
            'cedula_num'       => 'required|numeric|digits_between:6,9',
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'correo'           => 'required|email|unique:users,correo',
            'prefijo_telefono' => 'required|in:0412,0414,0424,0416,0426',
            'telefono_num'     => 'required|numeric|digits:7',
            'contraseña'       => 'required|min:6|confirmed',
        ]);

        $cedulaUnificada   = "{$request->nacionalidad}-{$request->cedula_num}";
        $telefonoUnificado = "{$request->prefijo_telefono}{$request->telefono_num}";

        // Clave única para evitar carreras críticas (race conditions) en el mismo milisegundo
        $lockKey = 'register_lock_' . md5($cedulaUnificada . $request->correo);

        // Intenta obtener un candado atómico por 5 segundos
        return Cache::lock($lockKey, 5)->get(function () use ($request, $cedulaUnificada, $telefonoUnificado) {
            
            if (User::where('cedula', $cedulaUnificada)->exists()) {
                return back()->withErrors(['cedula_num' => 'Esta cédula o RIF ya se encuentra registrada.'])->withInput();
            }

            $user = DB::transaction(function () use ($request, $cedulaUnificada, $telefonoUnificado) {
                $roleCiudadano = Role::where('nombre', 'ciudadano')->first();

                return User::create([
                    'role_id'           => $roleCiudadano->id_roles ?? 4,
                    'direccion_id'      => null,
                    'cedula'            => $cedulaUnificada,
                    'telefono'          => $telefonoUnificado,
                    'nombre'            => $request->nombre,
                    'apellido'          => $request->apellido,
                    'correo'            => $request->correo,
                    'password'          => Hash::make($request->contraseña),
                    'email_verified_at' => null, // Inicia sin verificar hasta que haga clic en el correo
                ]);
            });

            // Generar URL firmada de verificación con duración de 60 minutos
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                ['id' => $user->id, 'hash' => sha1($user->correo)]
            );

            // Enviar correo de verificación institucional
            Mail::send('emails.plantilla-base', [
                'titulo'      => 'Verificación de Cuenta — Alcaldía de Carrizal',
                'subtitulo'   => 'Confirma tu dirección de correo electrónico',
                'contenido'   => "Estimado/a <strong>{$user->nombre} {$user->apellido}</strong>,<br><br>Gracias por registrarte en la plataforma de la Alcaldía del Municipio Carrizal. Para completar tu registro y activar tu cuenta de ciudadano, haz clic en el botón inferior.<br><br>Este enlace expirará en 60 minutos.",
                'textoAccion' => 'Verificar mi Correo',
                'urlAccion'   => $verificationUrl
            ], function ($message) use ($user) {
                $message->to($user->correo)
                        ->subject('Verifica tu correo electrónico — Alcaldía de Carrizal');
            });

            // Registro en la Bitácora de Auditoría para el Registro exitoso
            BitacoraEvento::registrar([
                'modulo'      => 'auth',
                'tipo_evento' => 'REGISTER',
                'nivel'       => 'info',
                'descripcion' => "Nuevo usuario registrado: {$user->nombre} {$user->apellido} ({$user->correo}).",
                'model'       => $user,
            ]);

            return redirect()->route('verification.notice')
                             ->with('correo_pendiente', $user->correo)
                             ->with('success', '¡Registro exitoso! Hemos enviado un enlace de activación a tu correo.');
        }) ?: back()->withErrors(['correo' => 'Se está procesando una solicitud idéntica. Por favor espera un momento.'])->withInput();
    }

    /**
     * Permite actualizar el correo si el usuario cometió un error tipográfico
     * y se encuentra en la vista de espera sin verificar.
     */
    public function updatePendingEmail(Request $request)
    {
        $request->validate([
            'correo_actual' => 'required|email',
            'nuevo_correo'  => 'required|email|unique:users,correo',
        ]);

        $user = User::where('correo', $request->correo_actual)->first();

        if (!$user || !is_null($user->email_verified_at)) {
            return back()->withErrors(['nuevo_correo' => 'No se pudo actualizar el correo. Es posible que la cuenta ya esté verificada o no exista.']);
        }

        // Actualizar el correo
        $user->correo = $request->nuevo_correo;
        $user->save();

        // Generar nueva URL firmada
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->correo)]
        );

        // Enviar correo de verificación al nuevo destino
        Mail::send('emails.plantilla-base', [
            'titulo'      => 'Verificación de Cuenta — Alcaldía de Carrizal',
            'subtitulo'   => 'Confirma tu nueva dirección de correo electrónico',
            'contenido'   => "Estimado/a <strong>{$user->nombre} {$user->apellido}</strong>,<br><br>Has actualizado tu dirección de correo electrónico. Para activar tu cuenta, haz clic en el botón inferior.<br><br>Este enlace expirará en 60 minutos.",
            'textoAccion' => 'Verificar mi Correo',
            'urlAccion'   => $verificationUrl
        ], function ($message) use ($user) {
            $message->to($user->correo)
                    ->subject('Verifica tu correo electrónico — Alcaldía de Carrizal');
        });

        return redirect()->route('verification.notice')
                         ->with('correo_pendiente', $user->correo)
                         ->with('success', '¡Correo actualizado exitosamente! Hemos enviado un nuevo enlace de activación a tu dirección corregida.');
    }

    /**
     * Cierra la sesión activa del usuario.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Registro en la Bitácora de Auditoría para el Cierre de Sesión
            BitacoraEvento::registrar([
                'modulo'      => 'auth',
                'tipo_evento' => 'LOGOUT',
                'nivel'       => 'info',
                'descripcion' => "El usuario {$user->nombre} {$user->apellido} cerró sesión en el sistema.",
                'model'       => $user,
            ]);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}