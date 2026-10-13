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
        $query = Solicitud::with(['user', 'inspector.user', 'recaudos']);

        // Filtros de búsqueda para Gestión Ambiental
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
        
        // Métricas rápidas para las tarjetas ejecutivas
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

        // Filtros de búsqueda para Residuos Comerciales
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
        
        // Métricas de Residuos
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
        $tipoRegistro = $request->input('tipo', 'denuncia'); // 'denuncia' o 'solicitud'
        
        if ($tipoRegistro === 'solicitud') {
            $query = Solicitud::with(['user', 'inspector.user', 'recaudos']);
            
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
            // Para denuncias cargamos exclusivamente las relaciones válidas
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
}