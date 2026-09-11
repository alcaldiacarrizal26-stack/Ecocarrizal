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

        // Obtener funcionarios (Directores e Inspectores) activos con sus relaciones
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
            'inspecciones_mes' => Denuncia::where('estatus', 'en_revision')
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
     * Asignar un funcionario a la denuncia y actualizar el estatus.
     */
    public function asignar(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $funcionario = User::with('inspector')->findOrFail($request->user_id);

        // Si el usuario seleccionado tiene perfil de inspector, guardamos su ID de inspector
        if ($funcionario->inspector) {
            $denuncia->inspector_id = $funcionario->inspector->id;
        } else {
            // Opcional: Si es un director sin tabla de inspector asociada, puedes manejarlo o requerir que sea inspector
            return redirect()->back()->with('error', 'El usuario seleccionado no cuenta con un perfil de inspector válido.');
        }

        $denuncia->estatus = 'en_revision';
        $denuncia->save();

        return redirect()->back()->with('success', 'Funcionario asignado correctamente.');
    }
}