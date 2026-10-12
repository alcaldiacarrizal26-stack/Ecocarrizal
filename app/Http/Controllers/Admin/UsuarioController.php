<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\BitacoraEvento;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['role', 'direccion'])->latest();

        // Filtro por búsqueda (nombre, apellido, cédula o correo)
        if ($request->filled('buscar')) {
            $busq = $request->buscar;
            $query->where(function($q) use ($busq) {
                $q->where('nombre', 'like', "%{$busq}%")
                  ->orWhere('apellido', 'like', "%{$busq}%")
                  ->orWhere('cedula', 'like', "%{$busq}%")
                  ->orWhere('correo', 'like', "%{$busq}%");
            });
        }

        // Filtro por rol
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtro por estatus (activo / inactivo)
        if ($request->filled('estatus')) {
            $query->where('is_active', $request->estatus === 'activo');
        }

        $usuarios = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.usuarios.index', compact('usuarios', 'roles'));
    }

    public function toggleStatus(User $usuario)
    {
        // Evitar que el admin se desactive a sí mismo por seguridad
        if ($usuario->id === auth()->id()) {
            return back()->withErrors(['error' => 'No puedes modificar tu propio estatus de acceso activo.']);
        }

        $usuario->is_active = !$usuario->is_active;
        $usuario->save();

        $estadoTexto = $usuario->is_active ? 'ACTIVADO' : 'SUSPENDIDO';

        // Registrar en bitácora
        BitacoraEvento::registrar([
            'modulo' => 'usuarios',
            'tipo_evento' => 'CAMBIO_ESTATUS',
            'nivel' => 'warning',
            'descripcion' => "El usuario {$usuario->nombre} {$usuario->apellido} ({$usuario->correo}) ha sido {$estadoTexto} por el administrador.",
            'model' => $usuario,
        ]);

        return back()->with('success', "¡El estatus del usuario ha sido actualizado a {$estadoTexto} correctamente!");
    }
}