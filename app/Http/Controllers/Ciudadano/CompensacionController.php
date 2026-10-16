<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Compensacion;
use App\Models\EntregaCompensacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $compensacion = Compensacion::where('id', $request->compensacion_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validar que no entregue más de la deuda restante
        $pendiente = $compensacion->cantidad_asignada - $compensacion->cantidad_entregada;
        if ($request->cantidad > $pendiente) {
            return redirect()->back()->withErrors([
                'cantidad' => "La cantidad ingresada ({$request->cantidad}) supera la cantidad pendiente ({$pendiente})."
            ]);
        }

        // Guardar archivo
        $path = $request->file('comprobante')->store('compensaciones/comprobantes', 'public');

        // Registrar entrega/abono
        EntregaCompensacion::create([
            'compensacion_id'       => $compensacion->id,
            'user_id'               => Auth::id(),
            'cantidad_entregada'    => $request->cantidad,
            'especie_entregada'     => $request->tipo_especie,
            'comprobante_path'      => $path,
            'observacion_ciudadano' => $request->observacion,
            'estatus'               => 'PENDIENTE',
        ]);

        return redirect()->back()->with('success', 'Abono de compensación registrado exitosamente. En espera de verificación por la Dirección de Ambiente.');
    }

    public function donar(Request $request)
    {
        $request->validate([
            'tipo_especie'    => 'required|string|max:255',
            'cantidad'        => 'required|integer|min:1',
            'observaciones'   => 'nullable|string|max:500',
            'comprobante'     => 'required|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        // Guardar archivo de comprobante
        $path = $request->file('comprobante')->store('compensaciones/donaciones', 'public');

        // Registrar la compensación como donación voluntaria en estado PENDIENTE de revisión
        Compensacion::create([
            'user_id'              => Auth::id(),
            'codigo_resolucion'    => 'DONACION-' . date('Y') . '-' . strtoupper(uniqid()),
            'tipo_tramite'         => 'DONACION',
            'especie_requerida'    => $request->tipo_especie,
            'cantidad_asignada'    => $request->cantidad,
            'cantidad_entregada'   => $request->cantidad,
            'comprobante_path'     => $path,
            'observacion'          => $request->observaciones,
            'estatus'              => 'PENDIENTE', // <--- Requiere aprobación del director
        ]);

        return redirect()->back()->with('success', 'Donación voluntaria registrada exitosamente. Queda pendiente de verificación y aprobación por la Dirección de Ambiente.');
    }
}