<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Direccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
     * Procesa el inicio de sesión del usuario.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'correo'     => ['required', 'string', 'email'],
            'contraseña' => ['required', 'string'],
        ]);

        // Pasamos 'correo' para buscar el usuario en la BD y 'password' para que Laravel valide el hash
        $attemptData = [
            'correo'   => $credentials['correo'],
            'password' => $credentials['contraseña'],
        ];

        if (Auth::attempt($attemptData, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    /**
     * Procesa el registro de nuevos usuarios o empresas.
     */
    public function register(Request $request)
    {
    // Validación de ejemplo
        $request->validate([
            'nacionalidad' => 'required|in:V,E,J,G',
            'cedula_num' => 'required|numeric|digits_between:6,9',
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:users,correo',
            'prefijo_telefono' => 'required|in:0412,0414,0424,0416,0426',
            'telefono_num' => 'required|numeric|digits:7',
            'contraseña' => 'required|min:6|confirmed',
        ]);

        $roleCiudadano = Role::where('nombre', 'ciudadano')->first();

        // Unificación de campos
        $cedulaUnificada = "{$request->nacionalidad}-{$request->cedula_num}";
        $telefonoUnificado = "{$request->prefijo_telefono}{$request->telefono_num}";

        // Verificar si ya existe la cédula unificada por seguridad
        if (User::where('cedula', $cedulaUnificada)->exists()) {
            return back()->withErrors(['cedula_num' => 'Esta cédula o RIF ya se encuentra registrada.'])->withInput();
        }

        $user = User::create([
            'role_id' => $roleCiudadano->id_roles,
            'direccion_id' => null,
            'cedula' => $cedulaUnificada,
            'telefono' => $telefonoUnificado,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'password' => Hash::make($request->contraseña),
        ]);

        Auth::login($user);

        return redirect()->back()->with('success', '¡Usuario creado exitosamente!');
    }

    /**
     * Cierra la sesión activa del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}