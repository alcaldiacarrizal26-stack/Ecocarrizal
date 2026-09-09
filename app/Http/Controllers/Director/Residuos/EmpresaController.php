<?php

namespace App\Http\Controllers\Director\Residuos;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresas = Empresa::with(['user', 'perfilAmbiental'])
            ->latest()
            ->paginate(15);

        return view('director.residuos.empresas', compact('empresas'));
    }

    /**
     * Muestra la vista detallada del expediente para revisión del Director.
     */
    public function show(Empresa $empresa)
    {
        // Carga de relaciones completas para la revisión técnica
        $empresa->load(['user', 'perfilAmbiental', 'documentosExpediente']);

        return view('director.residuos.show', compact('empresa'));
    }

    /**
     * Procesa el veredicto del Director (Aprobado, Rechazado u Observado).
     */
    public function evaluarEmpresa(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'estatus' => 'required|in:APROBADO,RECHAZADO,OBSERVADO',
            'observaciones_fiscalia' => 'required_if:estatus,RECHAZADO,OBSERVADO|nullable|string|max:1000',
        ], [
            'observaciones_fiscalia.required_if' => 'Es obligatorio indicar el motivo cuando el veredicto es Rechazado u Observado.',
        ]);

        $empresa->update([
            'estatus' => $validated['estatus'],
            'observaciones_fiscalia' => $validated['observaciones_fiscalia'] ?? null,
        ]);

        return redirect()
            ->route('director.empresas')
            ->with('success', 'El veredicto para la empresa ' . $empresa->razon_social . ' ha sido registrado.');
    }
}