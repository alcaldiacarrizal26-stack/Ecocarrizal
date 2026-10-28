<?php

namespace App\Http\Controllers\Director;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
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
        $inspectores = Inspector::with(['user', 'direccion', 'solicitudes' => function ($query) {
            $query->whereIn('estado', ['inspector_asignado', 'en_revision']);
        }, 'denuncias' => function ($query) {
            $query->whereIn('estatus', ['inspector_asignado', 'en_revision']);
        }])->get();

        return view('director.inspectores', compact('inspectores'));
    }

    public function toggleStatus($id)
    {
        $inspector = Inspector::findOrFail($id);
        
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
            'codigo_credencial' => 'required|string|max:50|unique:inspectores,codigo_credencial',
            'especialidad'      => 'nullable|string|max:100',
        ]);

        $lockKey = 'inspector_store_lock_' . md5($request->cedula . $request->correo);

        return Cache::lock($lockKey, 5)->get(function () use ($request, $direccionId) {
            DB::transaction(function () use ($request, $direccionId) {
                $roleInspector = Role::where('nombre', 'inspector')->first();
                $roleId = $roleInspector->id_roles ?? 3;

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
                    'email_verified_at' => null, // Inicia sin verificar hasta que configure su contraseña
                ]);

                Inspector::create([
                    'user_id'           => $user->id,
                    'direccion_id'      => $direccionId,
                    'codigo_credencial' => $request->codigo_credencial,
                    'especialidad'      => $request->especialidad,
                    'disponible'        => true,
                ]);

                // 1. Generar token de seguridad de Laravel
                $token = Password::broker()->createToken($user);

                // 2. Construir URL absoluta para configurar la contraseña
                $url = route('password.reset', [
                    'token' => $token,
                    'correo' => $user->correo,
                ]);

                // 3. Enviar correo con presentación exclusiva para nuevos inspectores
                Mail::send('emails.plantilla-base', [
                    'titulo'      => 'Bienvenido al Sistema — Alcaldía de Carrizal',
                    'subtitulo'   => 'Acreditación y Activación de Cuenta de Inspector',
                    'contenido'   => "Estimado/a <strong>{$user->nombre} {$user->apellido}</strong>,<br><br>Has sido registrado formalmente como Inspector Activo en la plataforma de gestión de la Alcaldía del Municipio Carrizal.<br><br>Para comenzar a operar en el módulo de campo, haz clic en el botón inferior para configurar tu contraseña de acceso por primera vez.<br><br>Este enlace de activación es de uso único y expirará en 60 minutos.",
                    'textoAccion' => 'Configurar mi Contraseña',
                    'urlAccion'   => $url
                ], function ($message) use ($user) {
                    $message->to($user->correo)
                            ->subject('Activación de Cuenta de Inspector — Alcaldía de Carrizal');
                });
            });

            return redirect()->route('director.inspectores.index')
                ->with('success', 'Inspector registrado exitosamente. Se le ha enviado un correo de activación para que configure su contraseña.');
        }) ?: back()->withErrors(['correo' => 'Se está procesando un registro idéntico en este momento. Por favor espera.'])->withInput();
    }

    public function update(Request $request, $id)
    {
        $inspector = Inspector::with('user')->findOrFail($id);
        $user = $inspector->user;

        // Validar si el correo ya fue verificado (cuenta activada) para bloquear la edición por completo
        if (!is_null($user->email_verified_at)) {
            return redirect()->back()->withErrors(['error' => 'Este inspector ya ha activado su cuenta y su perfil se encuentra bloqueado contra modificaciones por seguridad.']);
        }

        $request->validate([
            'cedula'            => 'required|string|max:20|unique:users,cedula,' . $user->id,
            'nombre'            => 'required|string|max:100',
            'apellido'          => 'required|string|max:100',
            'correo'            => 'required|email|max:255|unique:users,correo,' . $user->id,
            'telefono'          => 'required|string|max:15',
            'codigo_credencial' => 'required|string|max:50|unique:inspectores,codigo_credencial,' . $inspector->id,
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

        return redirect()->route('director.inspectores.index')
            ->with('success', 'Datos del inspector actualizados exitosamente.');
    }
}