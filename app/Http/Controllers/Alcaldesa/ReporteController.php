<?php

namespace App\Http\Controllers\Alcaldesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Denuncia;
use App\Models\Solicitud;
use App\Models\Empresa;
use App\Models\DeclaracionResiduo;
use App\Models\Compensacion;

class ReporteController extends Controller
{
    /**
     * Dashboard General Ejecutivo (Resumen de las 3 áreas)
     */
    public function index(Request $request)
    {
        $totalDenuncias = Denuncia::count();
        $denunciasPorComuna = Denuncia::select('comuna', \DB::raw('count(*) as total'))
            ->groupBy('comuna')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $totalSolicitudesAmbiente = Solicitud::count();
        $compensacionesPendientes = Compensacion::where('estatus', 'PENDIENTE')->count();

        $totalEmpresasAprobadas = Empresa::where('estatus', 'APROBADO')->count();
        $toneladasTotalesComunes = DeclaracionResiduo::sum('toneladas_comunes');
        $toneladasTotalesReciclables = DeclaracionResiduo::sum('toneladas_reciclables');
        $toneladasTotalesPeligrosas = DeclaracionResiduo::sum('toneladas_peligrosas');

        $ultimasDenuncias = Denuncia::latest()->take(5)->get();
        $ultimasDeclaraciones = DeclaracionResiduo::with('empresa')->latest()->take(5)->get();

        return view('alcaldesa.reportes', compact(
            'totalDenuncias',
            'denunciasPorComuna',
            'totalSolicitudesAmbiente',
            'compensacionesPendientes',
            'totalEmpresasAprobadas',
            'toneladasTotalesComunes',
            'toneladasTotalesReciclables',
            'toneladasTotalesPeligrosas',
            'ultimasDenuncias',
            'ultimasDeclaraciones'
        ));
    }

    /**
     * Detalle 1: División de Ambiente
     */
    public function ambiente(Request $request)
    {
        $query = Solicitud::with(['usuario', 'inspector.user', 'recaudos']);

        if ($request->filled('tipo_tramite')) {
            $query->where('tipo_tramite', $request->tipo_tramite);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $solicitudes = $query->latest()->paginate(15)->withQueryString();
        
        $totalTramites = Solicitud::count();
        $tramitesAprobados = Solicitud::where('estado', 'aprobado')->count();
        $compensacionesPendientes = Compensacion::where('estatus', 'PENDIENTE')->count();
        $totalCompensaciones = Compensacion::count();

        $compensaciones = Compensacion::with(['user', 'solicitud'])->latest()->take(10)->get();

        return view('alcaldesa.ambiente', compact(
            'solicitudes',
            'totalTramites',
            'tramitesAprobados',
            'compensacionesPendientes',
            'totalCompensaciones',
            'compensaciones'
        ));
    }

    /**
     * Detalle 2: División de Residuos Comerciales (RACDA / Declaraciones)
     */
    public function residuos(Request $request)
    {
        $query = Empresa::with(['user', 'perfilAmbiental']);

        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('razon_social', 'ilike', "%{$busqueda}%")
                  ->orWhere('rif', 'ilike', "%{$busqueda}%");
            });
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        $empresas = $query->latest()->paginate(15)->withQueryString();
        
        $totalEmpresas = Empresa::count();
        $empresasAprobadas = Empresa::where('estatus', 'APROBADO')->count();
        $empresasPendientes = Empresa::whereIn('estatus', ['PENDIENTE', 'PENDIENTE_APROBACION'])->count();
        
        $declaraciones = DeclaracionResiduo::with('empresa')->latest()->take(10)->get();

        $totalesPorCategoria = [
            'comunes' => DeclaracionResiduo::sum('toneladas_comunes'),
            'reciclables' => DeclaracionResiduo::sum('toneladas_reciclables'),
            'peligrosas' => DeclaracionResiduo::sum('toneladas_peligrosas'),
        ];

        return view('alcaldesa.residuos', compact(
            'empresas',
            'totalEmpresas',
            'empresasAprobadas',
            'empresasPendientes',
            'declaraciones',
            'totalesPorCategoria'
        ));
    }

    /**
     * Detalle 3: División de Participación Ciudadana y Poder Popular (Comunas y Sectores)
     */
    public function poderPopular(Request $request)
    {
        $tipoRegistro = $request->input('tipo', 'denuncia');
        
        if ($tipoRegistro === 'solicitud') {
            $query = Solicitud::with(['usuario', 'inspector.user', 'recaudos']);
            
            if ($request->filled('codigo')) {
                $query->where('codigo_expediente', 'ilike', '%' . $request->codigo . '%');
            }
            if ($request->filled('comuna')) {
                $query->where('comuna', $request->comuna);
            }
            if ($request->filled('fecha_inicio')) {
                $query->whereDate('created_at', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $query->whereDate('created_at', '<=', $request->fecha_fin);
            }

            $registros = $query->latest()->paginate(15)->withQueryString();
            $comunasList = Solicitud::select('comuna')->distinct()->pluck('comuna');
        } else {
            $query = Denuncia::with(['user', 'inspector.user']);
            
            if ($request->filled('codigo')) {
                $query->where('codigo', 'ilike', '%' . $request->codigo . '%');
            }
            if ($request->filled('comuna')) {
                $query->where('comuna', $request->comuna);
            }
            if ($request->filled('gravedad')) {
                $query->where('nivel_gravedad', $request->gravedad);
            }
            if ($request->filled('fecha_inicio')) {
                $query->whereDate('created_at', '>=', $request->fecha_inicio);
            }
            if ($request->filled('fecha_fin')) {
                $query->whereDate('created_at', '<=', $request->fecha_fin);
            }

            $registros = $query->latest()->paginate(15)->withQueryString();
            $comunasList = Denuncia::select('comuna')->distinct()->pluck('comuna');
        }

        return view('alcaldesa.poder-popular', compact(
            'registros',
            'comunasList',
            'tipoRegistro'
        ));
    }

    public function exportarPoderPopularPdf(Request $request)
    {
        $tipoRegistro = $request->input('tipo', 'denuncia');
        
        $rankingComunas = ($tipoRegistro === 'solicitud' ? Solicitud::query() : Denuncia::query())
            ->select('comuna', 'sector', \DB::raw('count(*) as total'))
            ->groupBy('comuna', 'sector')
            ->orderByDesc('total')
            ->get();

        $motivosPrincipales = ($tipoRegistro === 'solicitud' ? Solicitud::query() : Denuncia::query())
            ->select(
                $tipoRegistro === 'solicitud' ? 'tipo_tramite as motivo' : 'tipo_contaminacion as motivo', 
                \DB::raw('count(*) as total')
            )
            ->groupBy('motivo')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $registrosGenerales = ($tipoRegistro === 'solicitud' 
            ? Solicitud::with('inspecciones') 
            : Denuncia::query()
        )->latest()->get();

        $totalReg = $registrosGenerales->count();
        
        $resueltos = $registrosGenerales->filter(function($item) use ($tipoRegistro) {
            $estatusCampo = $tipoRegistro === 'solicitud' ? ($item->estado ?? '') : ($item->estatus ?? '');
            return in_array(strtolower($estatusCampo), ['cerrado', 'aprobado', 'resuelto', 'completado']);
        })->count();
        $sinResolver = $totalReg - $resueltos;

        $maxMotivo = $motivosPrincipales->max('total') ?: 1;

        // Título dinámico para el informe
        $tituloInforme = $tipoRegistro === 'solicitud' 
            ? 'Informe Ejecutivo: Solicitudes Ambientales y Permisología' 
            : 'Informe Ejecutivo: Priorización del Poder Popular - Denuncias';

        // Gráfico 1: Estatus de Registros (Dona)
        $chartEstatusConfig = [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Resueltos / Aprobados', 'Pendientes / Sin Resolver'],
                'datasets' => [[
                    'data' => [$resueltos, $sinResolver],
                    'backgroundColor' => ['#059669', '#d97706'],
                    'borderWidth' => 0
                ]]
            ],
            'options' => [
                'legend' => ['display' => false],
                'plugins' => ['legend' => ['display' => false]],
                'cutoutPercentage' => 65
            ]
        ];
        $chartEstatusUrl = "https://quickchart.io/chart?w=120&h=120&c=" . urlencode(json_encode($chartEstatusConfig));

        if ($tipoRegistro === 'solicitud') {
            $dictamenAprobado = 0;
            $dictamenObservaciones = 0;
            $dictamenRechazado = 0;
            $sinInspeccion = 0;

            foreach ($registrosGenerales as $solicitud) {
                $inspeccion = $solicitud->inspecciones()->latest()->first();
                if (!$inspeccion) {
                    $sinInspeccion++;
                } else {
                    $dictamen = strtoupper($inspeccion->dictamen ?? '');
                    if ($dictamen === 'APROBADO') $dictamenAprobado++;
                    elseif ($dictamen === 'CON_OBSERVACIONES') $dictamenObservaciones++;
                    elseif (in_array($dictamen, ['RECHAZADO', 'REINSPECCION_REQUERIDA'])) $dictamenRechazado++;
                    else $sinInspeccion++;
                }
            }

            $labelGrafica2Title = "Veredictos de Inspección";
            $val1 = $dictamenAprobado;
            $val2 = $dictamenObservaciones;
            $val3 = $dictamenRechazado;
            $val4 = $sinInspeccion;
            
            $chartDictamenConfig = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => ['Aprobado', 'Con Observaciones', 'Rechazado', 'Sin Inspección'],
                    'datasets' => [[
                        'data' => [$val1, $val2, $val3, $val4],
                        'backgroundColor' => ['#059669', '#d97706', '#dc2626', '#94a3b8'],
                        'borderWidth' => 0
                    ]]
                ],
                'options' => [
                    'legend' => ['display' => false],
                    'plugins' => ['legend' => ['display' => false]],
                    'cutoutPercentage' => 65
                ]
            ];
            $chartDictamenUrl = "https://quickchart.io/chart?w=120&h=120&c=" . urlencode(json_encode($chartDictamenConfig));

            return view('alcaldesa.pdf.poder-popular', compact(
                'rankingComunas',
                'motivosPrincipales',
                'registrosGenerales',
                'tipoRegistro',
                'chartEstatusUrl',
                'chartDictamenUrl',
                'maxMotivo',
                'val1', 'val2', 'val3', 'val4',
                'labelGrafica2Title',
                'tituloInforme'
            ));

        } else {
            $gravedadAlta = $registrosGenerales->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'alta')->count();
            $gravedadMedia = $registrosGenerales->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'media')->count();
            $gravedadBaja = $registrosGenerales->filter(fn($i) => strtolower($i->nivel_gravedad ?? '') === 'baja')->count();

            $chartGravedadConfig = [
                'type' => 'doughnut',
                'data' => [
                    'labels' => ['Alta', 'Media', 'Baja'],
                    'datasets' => [[
                        'data' => [$gravedadAlta, $gravedadMedia, $gravedadBaja],
                        'backgroundColor' => ['#dc2626', '#d97706', '#2563eb'],
                        'borderWidth' => 0
                    ]]
                ],
                'options' => [
                    'legend' => ['display' => false],
                    'plugins' => ['legend' => ['display' => false]],
                    'cutoutPercentage' => 65
                ]
            ];
            $chartGravedadUrl = "https://quickchart.io/chart?w=120&h=120&c=" . urlencode(json_encode($chartGravedadConfig));
            $labelGrafica2Title = "Niveles de Gravedad";

            return view('alcaldesa.pdf.poder-popular', compact(
                'rankingComunas',
                'motivosPrincipales',
                'registrosGenerales',
                'tipoRegistro',
                'chartEstatusUrl',
                'chartGravedadUrl',
                'maxMotivo',
                'gravedadAlta',
                'gravedadMedia',
                'gravedadBaja',
                'labelGrafica2Title',
                'tituloInforme'
            ));
        }
    }
}