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
            $request->session()->regenerate();

            $user = Auth::user();

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
                    'role_id'      => $roleCiudadano->id_roles ?? 4,
                    'direccion_id' => null,
                    'cedula'       => $cedulaUnificada,
                    'telefono'     => $telefonoUnificado,
                    'nombre'       => $request->nombre,
                    'apellido'     => $request->apellido,
                    'correo'       => $request->correo,
                    'password'     => Hash::make($request->contraseña),
                ]);
            });

            Auth::login($user);

            // Registro en la Bitácora de Auditoría para el Registro exitoso
            BitacoraEvento::registrar([
                'modulo'      => 'auth',
                'tipo_evento' => 'REGISTER',
                'nivel'       => 'info',
                'descripcion' => "Nuevo usuario registrado y autenticado: {$user->nombre} {$user->apellido} ({$user->correo}).",
                'model'       => $user,
            ]);

            return redirect()->to('/dashboard')->with('success', '¡Usuario creado exitosamente!');
        }) ?: back()->withErrors(['correo' => 'Se está procesando una solicitud idéntica. Por favor espera un momento.'])->withInput();
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