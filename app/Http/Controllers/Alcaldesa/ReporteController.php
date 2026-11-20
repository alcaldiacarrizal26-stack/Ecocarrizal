<?php

namespace App\Http\Controllers\Alcaldesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Denuncia;
use App\Models\Solicitud;
use App\Models\Empresa;
use App\Models\DeclaracionResiduo;
use App\Models\Compensacion;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $totalDenuncias = Denuncia::count();
        $denunciasPorComuna = Denuncia::select('comuna', DB::raw('count(*) as total'))
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

    public function ambiente(Request $request)
    {
        $query = Solicitud::with(['usuario', 'inspector', 'recaudos', 'tipoTramite']);

        // CORREGIDO: Filtramos por la relación tipoTramite (slug) en lugar del campo string
        if ($request->filled('tipo_tramite')) {
            $query->whereHas('tipoTramite', function($q) use ($request) {
                $q->where('slug', $request->tipo_tramite);
            });
        }
        if ($request->filled('estado')) {
            $query->where('estado', strtoupper($request->estado)); // <-- MAYÚSCULAS
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $solicitudes = $query->latest()->paginate(15)->withQueryString();
        
        $totalTramites = Solicitud::count();
        $tramitesAprobados = Solicitud::where('estado', 'APROBADO')->count(); // <-- MAYÚSCULAS
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

    public function residuos(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfWeek()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->endOfWeek()->toDateString());

        $query = Empresa::with(['user', 'perfilAmbiental', 'documentosExpediente']);

        if ($request->filled('buscar')) {
            $busqueda = $request->input('buscar');
            $query->where(function($q) use ($busqueda) {
                $q->where('razon_social', 'ilike', "%{$busqueda}%")
                ->orWhere('nombre_comercial', 'ilike', "%{$busqueda}%")
                ->orWhere('rif', 'ilike', "%{$busqueda}%");
            });
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', strtoupper($request->input('estatus')));
        }

        if ($request->filled('comuna')) {
            $query->where('comuna', $request->input('comuna'));
        }

        if ($request->filled('rol')) {
            $rol = $request->input('rol');
            $query->whereHas('perfilAmbiental', function($q) use ($rol) {
                $q->where('rol_ambiental', strtoupper($rol));
            });
        }

        $empresas = $query->latest()->paginate(10, ['*'], 'empresas_page')->withQueryString();
        $totalEmpresas = Empresa::count();
        $comunasDisponibles = Empresa::whereNotNull('comuna')->distinct()->pluck('comuna');

        $declaracionesQuery = DeclaracionResiduo::with(['empresa.perfilAmbiental'])
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        if ($request->filled('rol_declarante')) {
            $declaracionesQuery->where('rol_declarante', strtoupper($request->input('rol_declarante')));
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

    public function poderPopular(Request $request)
    {
        $tipoRegistro = $request->input('tipo', 'denuncia');
        
        if ($tipoRegistro === 'solicitud') {
            $query = Solicitud::with(['usuario', 'inspector', 'recaudos']);
            
            if ($request->filled('codigo')) {
                $query->where('codigo_solicitud', 'ilike', '%' . $request->codigo . '%'); // <-- CORREGIDO
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
                $query->where('codigo_denuncia', 'ilike', '%' . $request->codigo . '%'); // <-- CORREGIDO
            }
            if ($request->filled('comuna')) {
                $query->where('comuna', $request->comuna);
            }
            if ($request->filled('gravedad')) {
                $query->where('nivel_gravedad', strtoupper($request->gravedad)); // <-- MAYÚSCULAS
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
            ->select('comuna', 'sector', DB::raw('count(*) as total'))
            ->groupBy('comuna', 'sector')
            ->orderByDesc('total')
            ->get();

        if ($tipoRegistro === 'solicitud') {
            // CORREGIDO: join para obtener el nombre del trámite ya que es un ID
            $motivosPrincipales = Solicitud::join('tipos_tramites', 'solicitudes.tipo_tramite_id', '=', 'tipos_tramites.id')
                ->select('tipos_tramites.nombre as motivo', DB::raw('count(*) as total'))
                ->groupBy('tipos_tramites.nombre')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        } else {
            $motivosPrincipales = Denuncia::select('tipo_contaminacion as motivo', DB::raw('count(*) as total'))
                ->groupBy('tipo_contaminacion')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }

        $registrosGenerales = ($tipoRegistro === 'solicitud' 
            ? Solicitud::with('inspecciones') 
            : Denuncia::query()
        )->latest()->get();

        $totalReg = $registrosGenerales->count();
        
        $resueltos = $registrosGenerales->filter(function($item) use ($tipoRegistro) {
            // CORREGIDO: Denuncias usan 'estado' y comparamos en MAYÚSCULAS
            $estatusCampo = $tipoRegistro === 'solicitud' ? ($item->estado ?? '') : ($item->estado ?? '');
            return in_array(strtoupper($estatusCampo), ['CERRADO', 'APROBADO', 'RESUELTO', 'COMPLETADO', 'ATENDIDO']);
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
            // CORREGIDO: UPPERCASE en las comparaciones
            $gravedadAlta = $registrosGenerales->filter(fn($i) => strtoupper($i->nivel_gravedad ?? '') === 'ALTA')->count();
            $gravedadMedia = $registrosGenerales->filter(fn($i) => strtoupper($i->nivel_gravedad ?? '') === 'MEDIA')->count();
            $gravedadBaja = $registrosGenerales->filter(fn($i) => strtoupper($i->nivel_gravedad ?? '') === 'BAJA')->count();

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
        $totalEmpresas = Empresa::count();
        $totalGeneradores = Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'GENERADOR'))->count();
        $totalManejadores = Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'MANEJADOR'))->count();
        $totalNoAplica = Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'NO_APLICA'))->count();

        $empresas = Empresa::with(['perfilAmbiental'])->get();
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

        $totalEmpresas = Empresa::count();
        $totalGeneradoras = Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'GENERADOR'))->count();
        $totalManejadoras = Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'MANEJADOR'))->count();
        $totalNoAplica = Empresa::whereHas('perfilAmbiental', fn($q) => $q->where('rol_ambiental', 'NO_APLICA'))->count();

        $totalesPorCategoria = [
            'comunes' => DeclaracionResiduo::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('toneladas_comunes'),
            'reciclables' => DeclaracionResiduo::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('toneladas_reciclables'),
            'peligrosas' => DeclaracionResiduo::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->sum('toneladas_peligrosas'),
        ];

        $empresas = Empresa::with(['declaracionesResiduos' => function($q) use ($fechaInicio, $fechaFin) {
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

        $query = Solicitud::with(['inspecciones', 'inspector', 'recaudos', 'tipoTramite'])
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        $solicitudes = $query->get();
        $totalTramites = $solicitudes->count();

        $tramitesSinCierre = $solicitudes->filter(fn($s) => is_null($s->fecha_cierre))->count();
        $tramitesSinInspector = $solicitudes->filter(fn($s) => is_null($s->inspector_id))->count();

        $resumenPorTipo = [];
        
        // CORREGIDO: Agrupamos por ID de trámite y obtenemos el nombre desde la relación
        $agrupadasPorTipo = $solicitudes->groupBy('tipo_tramite_id');

        foreach ($agrupadasPorTipo as $tipoId => $lista) {
            $nombreTramite = $lista->first()->tipoTramite->nombre ?? 'Desconocido';
            $estatusConteo = $lista->groupBy('estado')->map->count();
            $resumenPorTipo[$nombreTramite] = [
                'total' => $lista->count(),
                'estatus' => $estatusConteo,
            ];
        }

        $resumenPorComuna = [];
        $agrupadasPorComuna = $solicitudes->groupBy('comuna');

        foreach ($agrupadasPorComuna as $comuna => $listaComuna) {
            if (empty($comuna)) continue;
            $tiposEnComuna = $listaComuna->groupBy('tipo_tramite_id')->map(function($items) {
                return $items->first()->tipoTramite->nombre ?? 'Desconocido';
            })->countBy()->toArray(); 
            // Nota: countBy del mapeo anterior agrupa por el nombre resultante.
            
            $resumenPorComuna[$comuna] = [
                'tipos' => $tiposEnComuna,
                'total' => $listaComuna->count(),
            ];
        }

        // Métricas de Denuncias (UPPERCASE y campo 'estado')
        $denunciasQuery = Denuncia::whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        $totalDenuncias = $denunciasQuery->count();
        $denunciasRechazadas = (clone $denunciasQuery)->whereIn('estado', ['RECHAZADO', 'DESESTIMADO'])->count(); // <-- CORREGIDO
        $denunciasAtendidas = (clone $denunciasQuery)->where('estado', 'ATENDIDO')->count(); // <-- CORREGIDO

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