<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BitacoraEvento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        // Si no se han enviado fechas, inicializamos la colección vacía
        if (!$request->filled('fecha_desde') || !$request->filled('fecha_hasta')) {
            $bitacoras = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $modulos = BitacoraEvento::select('modulo')->distinct()->pluck('modulo');
            return view('admin.bitacora', compact('bitacoras', 'modulos'));
        }

        $query = BitacoraEvento::with('user.role')->latest();

        // Rango exacto abarcando desde el inicio del día "desde" hasta el final del día "hasta"
        $fechaDesde = Carbon::parse($request->fecha_desde)->startOfDay();
        $fechaHasta = Carbon::parse($request->fecha_hasta)->endOfDay();

        $query->whereBetween('created_at', [$fechaDesde, $fechaHasta]);

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->filled('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('descripcion', 'like', "%{$busqueda}%")
                  ->orWhere('tipo_evento', 'like', "%{$busqueda}%");
            });
        }

        $bitacoras = $query->paginate(15)->withQueryString();
        $modulos = BitacoraEvento::select('modulo')->distinct()->pluck('modulo');

        return view('admin.bitacora', compact('bitacoras', 'modulos'));
    }
}