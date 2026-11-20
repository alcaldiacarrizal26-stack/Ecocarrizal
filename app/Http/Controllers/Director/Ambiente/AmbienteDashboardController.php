<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Models\Solicitud;
use App\Models\CredencialInspector;
use Illuminate\Support\Facades\DB;

class AmbienteDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_solicitudes'      => Solicitud::count(),
            'solicitudes_pendientes' => Solicitud::where('estado', 'PENDIENTE')->count(), // <-- MAYÚSCULAS
            'total_denuncias'        => Denuncia::count(),
            'denuncias_altas'        => Denuncia::where('nivel_gravedad', 'ALTA')
                                        ->whereNotIn('estado', ['ATENDIDO', 'DESESTIMADO']) // <-- estado y MAYÚSCULAS
                                        ->count(),
        ];

        $denunciasUrgentes = Denuncia::where('nivel_gravedad', 'ALTA')
            ->whereNotIn('estado', ['ATENDIDO', 'DESESTIMADO'])
            ->latest()
            ->take(5)
            ->get();

        $solicitudesAntiguas = Solicitud::where('estado', 'PENDIENTE')
            ->oldest()
            ->take(5)
            ->get();

        // CredencialInspector en lugar de Inspector
        $inspectores = CredencialInspector::with('user')->get();

        // Gráfico: Distribución de Solicitudes por Tipo de Trámite (JOIN a tipos_tramites)
        $solicitudesGrouped = Solicitud::join('tipos_tramites', 'solicitudes.tipo_tramite_id', '=', 'tipos_tramites.id')
            ->select('tipos_tramites.nombre', DB::raw('count(*) as total'))
            ->groupBy('tipos_tramites.nombre')
            ->pluck('total', 'tipos_tramites.nombre')
            ->toArray();

        $categoriasSolicitudes = [
            'labels' => array_keys($solicitudesGrouped),
            'values' => array_values($solicitudesGrouped),
        ];

        // Gráfico: Categorización de Denuncias (ya vienen en mayúsculas)
        $denunciasGrouped = Denuncia::select('nivel_gravedad', DB::raw('count(*) as total'))
            ->groupBy('nivel_gravedad')
            ->pluck('total', 'nivel_gravedad')
            ->toArray();

        $gravedadDenuncias = [
            'labels' => array_map('ucfirst', array_keys($denunciasGrouped)),
            'values' => array_values($denunciasGrouped),
        ];

        return view('director.ambiente.dashboard', compact(
            'stats',
            'denunciasUrgentes',
            'solicitudesAntiguas',
            'inspectores',
            'categoriasSolicitudes',
            'gravedadDenuncias'
        ));
    }
}