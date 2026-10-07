<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Models\User;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $filtroEstatus = $request->get('estatus', 'todos');

        $query = Denuncia::with(['user', 'inspector.user']);

        if ($filtroEstatus !== 'todos') {
            $query->where('estatus', $filtroEstatus);
        }

        $denuncias = $query->latest()->paginate(10);

        // Obtener funcionarios activos con sus relaciones
        $funcionarios = User::whereHas('role', function ($q) {
                $q->whereIn('nombre', ['director', 'inspector']);
            })
            ->where('is_active', true)
            ->with(['role', 'direccion', 'inspector'])
            ->get();

        $stats = [
            'total_denuncias'  => Denuncia::count(),
            'atendidas'        => Denuncia::where('estatus', 'atendido')->count(),
            'pendientes'       => Denuncia::where('estatus', 'pendiente')->count(),
            'inspecciones_mes' => Denuncia::whereIn('estatus', ['inspector_asignado', 'en_revision'])
                                    ->whereMonth('created_at', now()->month)
                                    ->count(),
        ];

        return view('director.ambiente.reportes', compact(
            'denuncias',
            'funcionarios',
            'stats',
            'filtroEstatus'
        ));
    }

    /**
     * Asignar un inspector o director a la denuncia y actualizar el estatus y la fecha de asignación.
     */
    public function asignar(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $funcionario = User::with('inspector')->findOrFail($request->user_id);

        // Si el usuario es Director/Inspector pero no tiene perfil de inspector creado en la tabla 'inspectores', 
        // se crea automáticamente para poder mantener la integridad referencial (inspector_id en denuncias)
        $inspector = $funcionario->inspector;

        if (!$inspector) {
            $inspector = \App\Models\Inspector::create([
                'user_id' => $funcionario->id,
                'codigo_credencial' => 'DIR-' . str_pad($funcionario->id, 4, '0', STR_PAD_LEFT),
                'especialidad' => 'Dirección y Supervisión',
                'estatus' => 'activo'
            ]);
        }

        $denuncia->inspector_id = $inspector->id;
        $denuncia->estatus = 'inspector_asignado';
        $denuncia->fecha_asignacion_inspector = now();
        $denuncia->save();

        return redirect()->back()->with('success', 'Funcionario asignado correctamente a la denuncia.');
    }
}