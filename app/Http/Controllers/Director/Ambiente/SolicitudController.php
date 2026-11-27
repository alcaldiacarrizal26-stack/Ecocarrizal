<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Notificacion;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    public function index(Request $request)
    {
        // Conteo usando los Enums reales en MAYÚSCULAS
        $rawStats = Solicitud::select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $stats = [
            'total_solicitudes'  => array_sum($rawStats),
            'incompleta'         => $rawStats['INCOMPLETA'] ?? 0,
            'pendiente'          => $rawStats['PENDIENTE'] ?? 0,
            'inspector_asignado' => $rawStats['INSPECTOR_ASIGNADO'] ?? 0,
            'en_inspeccion'      => $rawStats['EN_INSPECCION'] ?? 0,
            'aprobada'           => $rawStats['APROBADO'] ?? 0,
            'rechazada'          => $rawStats['RECHAZADO'] ?? 0,
        ];

        // CORREGIDO: Se agregó 'tipoTramite' al Eager Loading para evitar N+1
        $query = Solicitud::with(['usuario', 'inspector', 'recaudos.recaudoCatalogo', 'tipoTramite']);

        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $solicitudes = $query->latest()->paginate(10)->withQueryString();

        $funcionarios = User::whereHas('role', function ($q) {
                $q->whereIn('nombre', ['director', 'inspector']);
            })
            ->where('is_active', true)
            ->with(['role', 'direccion', 'credencialInspector'])
            ->get();

        return view('director.ambiente.solicitudes', compact('solicitudes', 'stats', 'funcionarios'));
    }

    public function asignarInspector(Request $request, string $id)
    {
        $solicitud = Solicitud::with('usuario')->findOrFail($id);

        if (!in_array($solicitud->estado, ['PENDIENTE', 'INSPECTOR_ASIGNADO'])) {
            return redirect()->back()->with('error', 'Solo se pueden asignar funcionarios a solicitudes pendientes o con inspector asignado.');
        }

        $request->validate([
            'user_id' => [
                'required', 
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $valido = User::where('id', $value)
                        ->where('is_active', true)
                        ->whereHas('role', function ($q) {
                            $q->whereIn('nombre', ['director', 'inspector']);
                        })->exists();

                    if (!$valido) {
                        $fail('El funcionario seleccionado debe ser obligatoriamente un Director o un Inspector activo.');
                    }
                },
            ],
        ]);

        $solicitud->inspector_id               = $request->user_id;
        $solicitud->fecha_asignacion_inspector = now();

        if ($solicitud->estado === 'PENDIENTE') {
            $solicitud->estado = 'INSPECTOR_ASIGNADO';
        }

        $solicitud->save();

        if ($solicitud->user_id) {
            Notificacion::create([
                'user_id'  => $solicitud->user_id,
                'titulo'   => 'Solicitud en Inspección',
                'mensaje'  => 'Se ha asignado un funcionario inspector a tu solicitud ambiental para su revisión en campo.',
                'tipo'     => 'INFORMACION',
                'link_url' => '#',
            ]);
        }

        return redirect()->back()->with('success', 'Funcionario asignado correctamente.');
    }

    public function show(string $id)
    {
        $solicitud = Solicitud::with(['usuario', 'inspector', 'recaudos.recaudoCatalogo', 'tipoTramite'])->findOrFail($id);
        return view('director.ambiente.solicitudes_show', compact('solicitud'));
    }

    public function export(Request $request)
    {
        return back()->with('success', 'Listado de solicitudes exportado exitosamente.');
    }

    public function rechazar(Request $request, string $id)
    {
        $solicitud = Solicitud::with('usuario')->findOrFail($id);

        if ($solicitud->estado !== 'PENDIENTE') {
            return redirect()->back()->with('error', 'Solo se pueden rechazar solicitudes que se encuentren en estatus pendiente.');
        }

        $request->validate([
            'motivo_rechazo' => 'required|string|max:500',
        ]);

        $solicitud->estado        = 'RECHAZADO';
        $solicitud->observaciones = $request->motivo_rechazo;
        $solicitud->fecha_cierre  = now();
        $solicitud->save();

        if ($solicitud->user_id) {
            Notificacion::create([
                'user_id'  => $solicitud->user_id,
                'titulo'   => 'Solicitud de Trámite Rechazada',
                'mensaje'  => 'Tu solicitud de trámite ambiental ha sido rechazada. Motivo: ' . $request->motivo_rechazo,
                'tipo'     => 'ERROR',
                'link_url' => '#',
            ]);
        }

        return redirect()->back()->with('success', 'La solicitud ha sido rechazada exitosamente.');
    }
}