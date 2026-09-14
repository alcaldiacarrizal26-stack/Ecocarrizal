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
            // Incluye inspecciones en curso o recién asignadas en el mes
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
     * Asignar un inspector a la denuncia y actualizar el estatus.
     */
    public function asignar(Request $request, Denuncia $denuncia)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $funcionario = User::with('inspector')->findOrFail($request->user_id);

        if (!$funcionario->inspector) {
            return redirect()->back()->with('error', 'El usuario seleccionado no cuenta con un perfil de inspector válido.');
        }

        $denuncia->inspector_id = $funcionario->inspector->id;
        $denuncia->estatus = 'inspector_asignado'; // Se actualiza al estatus correspondiente tras asignar
        $denuncia->save();

        return redirect()->back()->with('success', 'Inspector asignado correctamente.');
    }
}