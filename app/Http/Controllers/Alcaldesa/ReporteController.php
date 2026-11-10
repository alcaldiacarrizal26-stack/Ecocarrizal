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
        $query = Solicitud::with(['usuario', 'inspector', 'recaudos']);

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
        $fechaInicio = $request->input('fecha_inicio', now()->startOfWeek()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->endOfWeek()->toDateString());

        $query = \App\Models\Empresa::with(['user', 'perfilAmbiental', 'documentosExpediente']);

        if ($request->filled('buscar')) {
            $busqueda = $request->input('buscar');
            $query->where(function($q) use ($busqueda) {
                $q->where('razon_social', 'ilike', "%{$busqueda}%")
                ->orWhere('nombre_comercial', 'ilike', "%{$busqueda}%")
                ->orWhere('rif', 'ilike', "%{$busqueda}%");
            });
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        if ($request->filled('comuna')) {
            $query->where('comuna', $request->input('comuna'));
        }

        if ($request->filled('rol')) {
            $rol = $request->input('rol');
            $query->whereHas('perfilAmbiental', function($q) use ($rol) {
                $q->where('rol_ambiental', $rol);
            });
        }

        $empresas = $query->latest()->paginate(10, ['*'], 'empresas_page')->withQueryString();
        $totalEmpresas = \App\Models\Empresa::count();
        $comunasDisponibles = \App\Models\Empresa::whereNotNull('comuna')->distinct()->pluck('comuna');

        $declaracionesQuery = \App\Models\DeclaracionResiduo::with(['empresa.perfilAmbiental'])
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        if ($request->filled('rol_declarante')) {
            $declaracionesQuery->where('rol_declarante', $request->input('rol_declarante'));
        }

        $declaraciones = $declaracionesQuery->latest()->paginate(10, ['*'], 'declaraciones_page')->withQueryString();

        $totalesPorCategoria = [
            'comunes' => (clone $declaracionesQuery)->sum('toneladas_comunes'),
            'reciclables' => (clone $declaracionesQuery)->sum('toneladas_reciclables'),
            'peligrosas' => (clone $declaracionesQuery)->sum('toneladas_peligrosas'),
        ];

        return view('alcaldesa.residuos', compact(
            'empresas', 
            'totalEmpresas', 
            'totalesPorCategoria', 
            'comunasDisponibles',
            'fechaInicio',
            'fechaFin',
            'declaraciones'
        ));
    }

    /**
     * Detalle 3: División de Participación Ciudadana y Poder Popular (Comunas y Sectores)
     */
    public function poderPopular(Request $request)
    {
        $tipoRegistro = $request->input('tipo', 'denuncia');
        
        if ($tipoRegistro === 'solicitud') {
            $query = Solicitud::with(['usuario', 'inspector', 'recaudos']);
            
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
            $query = Denuncia::with(['user', 'inspector']);
            
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
            return in_array(strtolower($estatusCampo), ['cerrado', 'aprobado', 'resuelto', 'completado', 'atendido']);
        })->count();
        $sinResolver = $totalReg - $resueltos;

        $maxMotivo = $motivosPrincipales->max('total') ?: 1;

        $tituloInforme = $tipoRegistro === 'solicitud' 
            ? 'Informe Ejecutivo: Solicitudes Ambientales y Permisología' 
            : 'Informe Ejecutivo: Priorización del Poder Popular - Denuncias';

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

    public function exportarEmpresasPdf()
    {
        $totalEmpresas = \App\Models\Empresa::count();
        $totalGeneradores = \App\Models\Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'GENERADOR'))->count();
        $totalManejadores = \App\Models\Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'MANEJADOR'))->count();
        $totalNoAplica = \App\Models\Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'NO_APLICA'))->count();

        $empresas = \App\Models\Empresa::with(['perfilAmbiental'])->get();
        $resumenComunasEmpresas = [];

        foreach ($empresas->groupBy('comuna') as $comuna => $lista) {
            if (empty($comuna)) continue;
            $resumenComunasEmpresas[$comuna] = [
                'generador' => $lista->filter(fn($e) => $e->perfilAmbiental?->rol_ambiental === 'GENERADOR')->count(),
                'manejador' => $lista->filter(fn($e) => $e->perfilAmbiental?->rol_ambiental === 'MANEJADOR')->count(),
                'no_aplica' => $lista->filter(fn($e) => $e->perfilAmbiental?->rol_ambiental === 'NO_APLICA')->count(),
                'total' => $lista->count(),
            ];
        }

        return view('alcaldesa.pdf.empresas', compact(
            'totalEmpresas', 
            'totalGeneradores', 
            'totalManejadores', 
            'totalNoAplica', 
            'resumenComunasEmpresas'
        ));
    }

    public function exportarResiduosPdf(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfWeek()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->endOfWeek()->toDateString());

        $totalEmpresas = \App\Models\Empresa::count();
        $totalGeneradoras = \App\Models\Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'GENERADOR'))->count();
        $totalManejadoras = \App\Models\Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'MANEJADOR'))->count();
        $totalNoAplica = \App\Models\Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'NO_APLICA'))->count();

        $totalesPorCategoria = [
            'comunes' => \App\Models\DeclaracionResiduo::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('toneladas_comunes'),
            'reciclables' => \App\Models\DeclaracionResiduo::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('toneladas_reciclables'),
            'peligrosas' => \App\Models\DeclaracionResiduo::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('toneladas_peligrosas'),
        ];

        $empresas = \App\Models\Empresa::with(['declaracionesResiduos' => function($q) use ($fechaInicio, $fechaFin) {
            $q->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        }, 'perfilAmbiental'])->get();
        
        $resumenPorComuna = [];
        $lideresPorComuna = [];
        $agrupadasPorComuna = $empresas->groupBy('comuna');

        foreach ($agrupadasPorComuna as $comuna => $listaEmpresas) {
            if (empty($comuna)) continue;

            $comunesComuna = 0;
            $reciclablesComuna = 0;
            $peligrosasComuna = 0;

            foreach ($listaEmpresas as $emp) {
                $comunesComuna += $emp->declaracionesResiduos->sum('toneladas_comunes');
                $reciclablesComuna += $emp->declaracionesResiduos->sum('toneladas_reciclables');
                $peligrosasComuna += $emp->declaracionesResiduos->sum('toneladas_peligrosas');
            }

            $resumenPorComuna[$comuna] = [
                'total_empresas' => $listaEmpresas->count(),
                'comunes' => $comunesComuna,
                'reciclables' => $reciclablesComuna,
                'peligrosas' => $peligrosasComuna,
                'total_toneladas' => $comunesComuna + $reciclablesComuna + $peligrosasComuna,
            ];

            $liderComun = $listaEmpresas->sortByDesc(fn($e) => $e->declaracionesResiduos->sum('toneladas_comunes'))->first();
            $liderReciclable = $listaEmpresas->sortByDesc(fn($e) => $e->declaracionesResiduos->sum('toneladas_reciclables'))->first();
            $liderPeligroso = $listaEmpresas->sortByDesc(fn($e) => $e->declaracionesResiduos->sum('toneladas_peligrosas'))->first();

            $lideresPorComuna[$comuna] = [
                'comun' => ($liderComun && $liderComun->declaracionesResiduos->sum('toneladas_comunes') > 0) ? (object)['razon_social' => $liderComun->razon_social, 'total' => $liderComun->declaracionesResiduos->sum('toneladas_comunes')] : null,
                'reciclable' => ($liderReciclable && $liderReciclable->declaracionesResiduos->sum('toneladas_reciclables') > 0) ? (object)['razon_social' => $liderReciclable->razon_social, 'total' => $liderReciclable->declaracionesResiduos->sum('toneladas_reciclables')] : null,
                'peligroso' => ($liderPeligroso && $liderPeligroso->declaracionesResiduos->sum('toneladas_peligrosas') > 0) ? (object)['razon_social' => $liderPeligroso->razon_social, 'total' => $liderPeligroso->declaracionesResiduos->sum('toneladas_peligrosas')] : null,
            ];
        }

        return view('alcaldesa.pdf.residuos', compact(
            'totalEmpresas', 
            'totalGeneradoras', 
            'totalManejadoras', 
            'totalNoAplica', 
            'totalesPorCategoria', 
            'resumenPorComuna', 
            'lideresPorComuna',
            'fechaInicio',
            'fechaFin'
        ));
    }

    public function exportarAmbientePdf(Request $request)
    {
        if (!$request->filled('fecha_inicio') || !$request->filled('fecha_fin')) {
            return back()->with('error', 'Debe seleccionar un rango de fechas válido para generar el informe de Gestión Ambiental.');
        }

        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Solicitud::with(['inspecciones', 'inspector', 'recaudos'])
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        $solicitudes = $query->get();
        $totalTramites = $solicitudes->count();

        $tramitesSinCierre = $solicitudes->filter(fn($s) => is_null($s->fecha_cierre))->count();
        $tramitesSinInspector = $solicitudes->filter(fn($s) => is_null($s->inspector_id))->count();

        $resumenPorTipo = [];
        $agrupadasPorTipo = $solicitudes->groupBy('tipo_tramite');

        foreach ($agrupadasPorTipo as $tipo => $lista) {
            $estatusConteo = $lista->groupBy('estado')->map->count();
            $resumenPorTipo[$tipo] = [
                'total' => $lista->count(),
                'estatus' => $estatusConteo,
            ];
        }

        $resumenPorComuna = [];
        $agrupadasPorComuna = $solicitudes->groupBy('comuna');

        foreach ($agrupadasPorComuna as $comuna => $listaComuna) {
            if (empty($comuna)) continue;
            $tiposEnComuna = $listaComuna->groupBy('tipo_tramite')->map->count();
            $resumenPorComuna[$comuna] = [
                'tipos' => $tiposEnComuna,
                'total' => $listaComuna->count(),
            ];
        }

        // Métricas de Denuncias en el mismo período para enriquecer el balance ambiental
        $denunciasQuery = Denuncia::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        $totalDenuncias = $denunciasQuery->count();
        $denunciasRechazadas = (clone $denunciasQuery)->whereIn('estatus', ['rechazado', 'desestimado'])->count();
        $denunciasAtendidas = (clone $denunciasQuery)->where('estatus', 'atendido')->count();

        $compensacionesQuery = Compensacion::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        $compensacionesADeber = (clone $compensacionesQuery)->where('estatus', 'PENDIENTE')->count();
        $compensacionesConcretadas = (clone $compensacionesQuery)->where('estatus', 'COMPLETADO')->count();
        $totalCompensaciones = $compensacionesQuery->count();

        return view('alcaldesa.pdf.ambiente', compact(
            'fechaInicio',
            'fechaFin',
            'totalTramites',
            'tramitesSinCierre',
            'tramitesSinInspector',
            'resumenPorTipo',
            'resumenPorComuna',
            'compensacionesADeber',
            'compensacionesConcretadas',
            'totalCompensaciones',
            'totalDenuncias',
            'denunciasRechazadas',
            'denunciasAtendidas'
        ));
    }
}