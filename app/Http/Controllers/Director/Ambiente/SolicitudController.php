<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Inspector;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        // 1. Conteo de estadísticas consolidado en una sola query
        $rawStats = Solicitud::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $stats = [
            'total_solicitudes'  => array_sum($rawStats),
            'incompleta'         => $rawStats['incompleta'] ?? 0,
            'pendiente'          => $rawStats['pendiente'] ?? 0,
            'inspector_asignado' => $rawStats['inspector_asignado'] ?? 0,
            'en_inspeccion'      => $rawStats['en_inspeccion'] ?? 0,
            'aprobada'           => $rawStats['aprobada'] ?? 0,
            'rechazada'          => $rawStats['rechazada'] ?? 0,
        ];

        // 2. Consulta de solicitudes filtradas y paginadas
        $query = Solicitud::with(['solicitante', 'inspector.user', 'recaudos']);

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->latest()->paginate(10)->withQueryString();

        // 3. Obtener funcionarios activos con perfil de inspector
        $funcionarios = User::whereHas('role', function ($q) {
                $q->whereIn('nombre', ['director', 'inspector']);
            })
            ->where('is_active', true)
            ->with(['role', 'direccion', 'inspector.user'])
            ->get();

        return view('director.ambiente.solicitudes', compact('solicitudes', 'stats', 'funcionarios'));
    }

    public function asignarInspector(Request $request, string $id)
    {
        $solicitud = Solicitud::findOrFail($id);

        if (!in_array($solicitud->estado, ['pendiente', 'inspector_asignado'])) {
            return redirect()->back()->with('error', 'Solo se pueden asignar o reasignar inspectores a solicitudes pendientes o con inspector asignado.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $funcionario = User::with('inspector')->findOrFail($request->user_id);

        if (!$funcionario->inspector) {
            return redirect()->back()->with('error', 'El usuario seleccionado no cuenta con un perfil de inspector.');
        }

        // Guarda la PK de la tabla inspectores (id = 1 en este caso)
        $solicitud->inspector_id = $funcionario->inspector->id;

        if ($solicitud->estado === 'pendiente') {
            $solicitud->estado = 'inspector_asignado';
        }

        $solicitud->save();

        return redirect()->back()->with('success', 'Inspector asignado correctamente.');
    }

    public function show(string $id)
    {
        $solicitud = Solicitud::with(['solicitante', 'inspector', 'recaudos'])->findOrFail($id);
        return view('director.ambiente.solicitudes_show', compact('solicitud'));
    }

    public function export(Request $request)
    {
        return back()->with('success', 'Listado de solicitudes exportado exitosamente.');
    }
}