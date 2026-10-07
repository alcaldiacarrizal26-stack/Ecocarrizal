<?php

namespace App\Http\Controllers\Director\Residuos;

use App\Http\Controllers\Controller;
use App\Models\DeclaracionResiduo;
use Illuminate\Http\Request;

class DeclaracionResiduoController extends Controller
{
    /**
     * Muestra el listado global de todas las declaraciones ambientales.
     */
    public function index(Request $request)
    {
        $query = DeclaracionResiduo::with(['empresa', 'detalles'])->latest();

        if ($request->filled('rol')) {
            $query->where('rol_declarante', $request->rol);
        }

        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        if ($request->filled('periodo')) {
            $query->where('periodo', 'like', '%' . $request->periodo . '%');
        }

        $declaraciones = $query->paginate(15);

        return view('director.residuos.empresas.declaraciones', compact('declaraciones'));
    }

    /**
     * Muestra el detalle particular de una declaración.
     */
    public function show(DeclaracionResiduo $declaracion)
    {
        $declaracion->load(['empresa', 'detalles', 'user']);

        return view('director.residuos.declaraciones.show', compact('declaracion'));
    }

    /**
     * Evalúa o actualiza el estatus de una declaración.
     */
    public function updateEstatus(Request $request, DeclaracionResiduo $declaracion)
    {
        $validated = $request->validate([
            'estatus'       => 'required|in:PRESENTADA,EN_REVISION,APROBADA,OBSERVADA,RECHAZADA',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $declaracion->update($validated);

        return back()->with('success', 'Estatus de la declaración actualizado correctamente.');
    }
}