<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\CredencialInspector;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InspectorController extends Controller
{
    public function index()
    {
        // Cargamos las credenciales con su usuario, y contamos las asignaciones a través del usuario
        $inspectores = CredencialInspector::with([
            'user.direccion',
            'user.solicitudesComoInspector' => function ($query) {
                $query->whereIn('estado', ['INSPECTOR_ASIGNADO', 'EN_REVISION']);
            },
            'user.denunciasComoInspector' => function ($query) {
                $query->whereIn('estado', ['INSPECTOR_ASIGNADO', 'EN_REVISION']);
            }
        ])->get();

        return view('director.inspectores', compact('inspectores'));
    }

    public function toggleStatus($id)
    {
        $inspector = CredencialInspector::findOrFail($id);
        
        $inspector->disponible = !$inspector->disponible;
        $inspector->save();

        return redirect()->back()->with('success', 'Estatus del inspector actualizado correctamente.');
    }

    public function store(Request $request)
    {
        $director = Auth::user();
        $direccionId = $director->direccion_id ?? 1;

        $request->validate([
            'cedula'            => 'required|string|max:20|unique:users,cedula',
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'required|string|max:100',
            'correo'            => 'required|email|max:255|unique:users,correo',
            'telefono'          => 'required|string|max:15',
            'codigo_credencial' => 'required|string|max:50|unique:credenciales_inspector,codigo_credencial', // <-- TABLA CORREGIDA
            'especialidad'      => 'nullable|string|max:100',
        ]);

        $lockKey = 'inspector_store_lock_' . md5($request->cedula . $request->correo);

        return Cache::lock($lockKey, 5)->get(function () use ($request, $direccionId) {
            DB::transaction(function () use ($request, $direccionId) {
                $roleInspector = Role::where('nombre', 'inspector')->first();
                $roleId = $roleInspector->id ?? 3; // <-- PK CORREGIDA

                $user = User::create([
                    'role_id'           => $roleId,
                    'direccion_id'      => $direccionId,
                    'cedula'            => $request->cedula,
                    'nombre'            => $request->nombre,
                    'apellido'          => $request->apellido,
                    'correo'            => $request->correo,
                    'telefono'          => $request->telefono,
                    'password'          => Hash::make(Str::random(32)),
                    'accepted_terms'    => true,
                    'is_active'         => true,
                    'email_verified_at' => null,
                ]);

                CredencialInspector::create([ // <-- MODELO CORREGIDO
                    'user_id'           => $user->id,
                    'codigo_credencial' => $request->codigo_credencial,
                    'especialidad'      => $request->especialidad,
                    'disponible'        => true,
                ]);

                $token = Password::broker()->createToken($user);

                $url = route('password.reset', [
                    'token' => $token,
                    'correo' => $user->correo,
                ]);

                Mail::send('emails.plantilla-base', [
                    'titulo'      => 'Bienvenido al Sistema — Alcaldía de Carrizal',
                    'subtitulo'   => 'Acreditación y Activación de Cuenta de Inspector',
                    'contenido'   => "Estimado/a <strong>{$user->nombre} {$user->apellido}</strong>,<br><br>Has sido registrado formalmente como Inspector Activo en la plataforma de gestión de la Alcaldía del Municipio Carrizal.<br><br>Para comenzar a operar en el módulo de campo, haz clic en el botón inferior para configurar tu contraseña de acceso por primera vez.<br><br>Este enlace de activación es de uso único y expirará en 60 minutos.",
                    'textoAccion' => 'Configurar mi Contraseña',
                    'urlAccion'   => $url
                ], function ($message) use ($user) {
                    $message->to($user->correo)->subject('Activación de Cuenta de Inspector — Alcaldía de Carrizal');
                });
            });

            return redirect()->route('director.inspectores.index')
                ->with('success', 'Inspector registrado exitosamente. Se le ha enviado un correo de activación.');
        }) ?: back()->withErrors(['correo' => 'Se está procesando un registro idéntico en este momento.'])->withInput();
    }

    public function update(Request $request, $id)
    {
        $inspector = CredencialInspector::with('user')->findOrFail($id);
        $user = $inspector->user;

        if (!is_null($user->email_verified_at)) {
            return redirect()->back()->withErrors(['error' => 'Este inspector ya ha activado su cuenta y su perfil se encuentra bloqueado.']);
        }

        $request->validate([
            'cedula'            => 'required|string|max:20|unique:users,cedula,' . $user->id,
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'required|string|max:100',
            'correo'            => 'required|email|max:255|unique:users,correo,' . $user->id,
            'telefono'          => 'required|string|max:15',
            'codigo_credencial' => 'required|string|max:50|unique:credenciales_inspector,codigo_credencial,' . $inspector->id, // <-- TABLA CORREGIDA
            'especialidad'      => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($request, $inspector, $user) {
            $user->update([
                'cedula'   => $request->cedula,
                'nombre'   => $request->nombre,
                'apellido' => $request->apellido,
                'correo'   => $request->correo,
                'telefono' => $request->telefono,
            ]);

            $inspector->update([
                'codigo_credencial' => $request->codigo_credencial,
                'especialidad'      => $request->especialidad,
            ]);
        });

        return redirect()->route('director.inspectores.index')->with('success', 'Datos del inspector actualizados exitosamente.');
    }
}