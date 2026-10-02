<?php

namespace App\Http\Controllers\Director\Residuos;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Muestra el listado general de empresas registradas para la Dirección de Residuos.
     */
    public function index(Request $request)
    {
        $query = Empresa::with(['user', 'perfilAmbiental']);

        // Filtro opcional por búsqueda (RIF o Razón Social)
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $query->where(function ($q) use ($buscar) {
                $q->where('rif', 'like', "%{$buscar}%")
                  ->orWhere('razon_social', 'like', "%{$buscar}%");
            });
        }

        // Filtro opcional por estatus de la empresa
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->input('estatus'));
        }

        $empresas = $query->latest()->paginate(15)->appends($request->all());

        return view('director.residuos.empresas.index', compact('empresas'));
    }

    /**
     * Muestra la vista detallada del expediente para revisión del Director.
     */
    public function show(Empresa $empresa)
    {
        // Carga de relaciones completas para la revisión técnica del expediente
        $empresa->load([
            'user', 
            'perfilAmbiental', 
            'documentosExpediente'
        ]);

        return view('director.residuos.empresas.show', compact('empresa'));
    }

    /**
     * Procesa la evaluación / veredicto del expediente de la empresa por parte del Director.
     */
    public function evaluar(Request $request, Empresa $empresa)
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
            ->route('director.residuos.empresas.show', $empresa->id)
            ->with('success', 'El veredicto para la empresa ' . $empresa->razon_social . ' ha sido registrado exitosamente.');
    }
}