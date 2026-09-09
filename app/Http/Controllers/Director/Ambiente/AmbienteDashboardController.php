<?php

namespace App\Http\Controllers\Director\Ambiente;

use App\Http\Controllers\Controller;
use App\Models\Denuncia;
use App\Models\Solicitud;
use App\Models\Inspector;
use Illuminate\Support\Facades\DB;

class AmbienteDashboardController extends Controller
{
    public function index()
    {
        // 1. Conteo para las Tarjetas (KPIs)
        $stats = [
            'total_solicitudes'     => Solicitud::count(),
            'solicitudes_pendientes' => Solicitud::where('estado', 'pendiente')->count(),
            'total_denuncias'       => Denuncia::count(),
            'denuncias_altas'       => Denuncia::where('nivel_gravedad', 'alta')
                                        ->whereNotIn('estatus', ['atendido', 'desestimado'])
                                        ->count(),
        ];

        // 2. Denuncias Críticas (Gravedad Alta activas)
        $denunciasUrgentes = Denuncia::where('nivel_gravedad', 'alta')
            ->whereNotIn('estatus', ['atendido', 'desestimado'])
            ->latest()
            ->take(5)
            ->get();

        // 3. Solicitudes Pendientes Antiguas
        $solicitudesAntiguas = Solicitud::where('estado', 'pendiente')
            ->oldest()
            ->take(5)
            ->get();

        // 4. Cuerpo de Inspectores
        $inspectores = Inspector::with('user')->get();

        // 5. Gráfico: Distribución de Solicitudes por Tipo de Trámite
        $solicitudesGrouped = Solicitud::select('tipo_tramite', DB::raw('count(*) as total'))
            ->groupBy('tipo_tramite')
            ->pluck('total', 'tipo_tramite')
            ->toArray();

        $categoriasSolicitudes = [
            'labels' => array_map(fn($tipo) => ucwords(str_replace('_', ' ', $tipo)), array_keys($solicitudesGrouped)),
            'values' => array_values($solicitudesGrouped),
        ];

        // 6. Gráfico: Categorización de Denuncias por Nivel de Gravedad
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