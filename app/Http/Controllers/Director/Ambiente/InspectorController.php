<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Inspector;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InspectorController extends Controller
{
    public function index()
    {
        $inspectores = Inspector::with([
            'user', 
            'denuncias' => function($q) {
                // Añade 'en_revision' o ajusta los estatus según corresponda
                $q->whereIn('estatus', ['en_revision', 'asignada', 'en_proceso']);
            }, 
            'solicitudes' => function($q) {
                $q->whereIn('estado', ['asignada', 'en_inspeccion']); 
            }
        ])->get();

        return view('director.ambiente.inspectores', compact('inspectores'));
    }

        public function toggleStatus($id)
    {
        $inspector = Inspector::findOrFail($id);
        
        // Cambiar estatus booleano
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
            'password'          => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request, $direccionId) {
            $user = User::create([
                'role_id'        => 3, // ID del rol Inspector
                'direccion_id'   => $direccionId,
                'cedula'         => $request->cedula,
                'nombre'         => $request->nombre,
                'apellido'       => $request->apellido,
                'correo'         => $request->correo,
                'telefono'       => $request->telefono,
                'password'       => Hash::make($request->password),
                'accepted_terms' => true,
                'is_active'       => true,
            ]);

            Inspector::create([
                'user_id'           => $user->id,
                'direccion_id'      => $direccionId,
                'codigo_credencial' => $request->codigo_credencial,
                'especialidad'      => $request->especialidad,
                'disponible'        => true,
            ]);
        });

        return redirect()->route('director.ambiente.inspectores')
            ->with('success', 'Inspector de Ambiente registrado exitosamente.');
    }
}