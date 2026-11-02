<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Compensacion;
use App\Models\EntregaCompensacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompensacionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $compensaciones = Compensacion::with('entregas')
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $stats = [
            'arboles_comprometidos' => (int) $compensaciones->sum('cantidad_asignada'),
            'arboles_entregados'    => (int) $compensaciones->sum('cantidad_entregada'),
            'pendientes_entrega'   => (int) ($compensaciones->sum('cantidad_asignada') - $compensaciones->sum('cantidad_entregada')),
        ];

        return view('ciudadano.compensacion', compact('compensaciones', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'compensacion_id' => 'required|exists:compensaciones,id',
            'tipo_especie'    => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:1',
            'observacion'     => 'nullable|string|max:500',
            'comprobante'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($request,$userId) {
            DB::statement('SELECT pg_advisory_xact_lock(?)', [crc32('compensacion_abono_' . $request->compensacion_id)]);

            $compensacion = Compensacion::where('id',$request->compensacion_id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $pendiente = $compensacion->cantidad_asignada -$compensacion->cantidad_entregada;
            if ($request->cantidad >$pendiente) {
                return redirect()->back()->withErrors([
                    'cantidad' => "La cantidad ingresada ({$request->cantidad}) supera la cantidad pendiente ({$pendiente})."
                ]);
            }

            $path =$request->file('comprobante')->store('compensaciones/comprobantes', 'public');

            // 1. Registrar entrega / abono
            EntregaCompensacion::create([
                'compensacion_id'       => $compensacion->id,
                'user_id'               => $userId,
                'cantidad_entregada'    => $request->cantidad,
                'especie_entregada'     => $request->tipo_especie,
                'comprobante_path'      => $path,
                'observacion_ciudadano' => $request->observacion,
                'estatus'               => 'PENDIENTE',
            ]);

            // 2. SUMAR Y ACTUALIZAR el total entregado en la compensación principal del ciudadano
            $compensacion->increment('cantidad_entregada',$request->cantidad);

            // 3. Notificar a la Dirección de Ambiente
            $directoresAmbiente = \App\Models\User::whereHas('role', function ($q) {$q->whereIn('nombre', ['admin', 'director', 'director_ambiente']);
            })->get();

            foreach ($directoresAmbiente as$dir) {
                \App\Models\Notificacion::create([
                    'user_id'  => $dir->id,
                    'titulo'   => 'Nuevo Abono de Compensación',
                    'mensaje'  => "Se ha registrado un abono de {$request->cantidad} ejemplares ({$request->tipo_especie}) pendiente de verificación.",
                    'tipo'     => 'success',
                    'link_url' => route('director.ambiente.compensacion'),
                ]);
            }

            return redirect()->back()->with('success', 'Abono de compensación registrado exitosamente. En espera de verificación por la Dirección de Ambiente.');
        });
    }

    public function donar(Request $request)
    {
        $request->validate([
            'tipo_especie'    => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:1',
            'observaciones'   => 'nullable|string|max:500',
            'comprobante'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $userId = Auth::id();

        return DB::transaction(function () use ($request,$userId) {
            $path =$request->file('comprobante')->store('compensaciones/donaciones', 'public');

            // Registrar donación como compensación independiente
            $donacion = Compensacion::create([
                'user_id'              => $userId,
                'codigo_resolucion'    => 'DONACION-' . date('Y') . '-' . strtoupper(uniqid()),
                'tipo_tramite'         => 'DONACION',
                'especie_requerida'    => $request->tipo_especie,
                'cantidad_asignada'    => $request->cantidad,
                'cantidad_entregada'   => $request->cantidad, // Al ser donación voluntaria se equipara al entregarse
                'comprobante_path'     => $path,
                'observacion'          => $request->observaciones,
                'estatus'              => 'PENDIENTE', 
            ]);

            // Notificar a la Dirección de Ambiente
            $directoresAmbiente = \App\Models\User::whereHas('role', function ($q) {$q->whereIn('nombre', ['admin', 'director', 'director_ambiente']);
            })->get();

            foreach ($directoresAmbiente as$dir) {
                \App\Models\Notificacion::create([
                    'user_id'  => $dir->id,
                    'titulo'   => 'Nueva Donación Voluntaria',
                    'mensaje'  => "Se ha registrado una donación voluntaria de {$request->cantidad} ejemplares de {$request->tipo_especie}.",
                    'tipo'     => 'success',
                    'link_url' => route('director.ambiente.compensacion'),
                ]);
            }

            return redirect()->back()->with('success', 'Donación voluntaria registrada exitosamente. Queda pendiente de verificación y aprobación por la Dirección de Ambiente.');
        });
    }
}