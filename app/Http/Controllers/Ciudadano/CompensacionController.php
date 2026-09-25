<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\Compensacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompensacionController extends Controller
{
    /**
     * Muestra la vista principal de compensación ambiental para el ciudadano.
     */
    public function index()
    {
        $userId = Auth::id();

        // Obtener las compensaciones del usuario autenticado
        $compensaciones = Compensacion::where('user_id', $userId)
            ->latest()
            ->get();

        // Calcular las métricas con base en los registros reales
        $stats = [
            'arboles_comprometidos' => (int) $compensaciones->sum('cantidad_asignada'),
            'arboles_entregados'    => (int) $compensaciones->sum('cantidad_entregada'),
            'pendientes_entrega'   => (int) ($compensaciones->sum('cantidad_asignada') - $compensaciones->sum('cantidad_entregada')),
        ];

        return view('ciudadano.compensacion', compact('compensaciones', 'stats'));
    }

    /**
     * Procesa la solicitud o registro de entrega de compensación ambiental.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_especie' => 'required|string|max:255',
            'cantidad'     => 'required|integer|min:1|max:1000',
            'observacion'  => 'nullable|string|max:500',
            'comprobante'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        return redirect()->back()->with('success', 'Registro de compensación ambiental procesado exitosamente.');
    }
}