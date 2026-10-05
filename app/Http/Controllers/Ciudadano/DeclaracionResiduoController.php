<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\DeclaracionResiduo;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeclaracionResiduoController extends Controller
{
    /**
     * Muestra el historial de declaraciones de la empresa del usuario.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtenemos la empresa del usuario (o redirige si aún no ha registrado una)
        $empresa = Empresa::where('user_id', $user->id)->first();

        if (!$empresa) {
            return redirect()->route('ciudadano.empresas.create')
                ->with('warning', 'Debes registrar una empresa antes de realizar declaraciones de residuos.');
        }

        $declaraciones = DeclaracionResiduo::where('empresa_id', $empresa->id)
            ->latest()
            ->paginate(10);

        return view('ciudadano.empresa.declaraciones', compact('empresa', 'declaraciones'));
    }

    /**
     * Formulario para crear una nueva declaración.
     */
    public function create()
    {
        $user = Auth::user();
        $empresa = Empresa::where('user_id', $user->id)->firstOrFail();

        return view('ciudadano.empresa.declaraciones.create', compact('empresa'));
    }

    /**
     * Guarda la declaración mensual/trimestral en BD.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $empresa = Empresa::where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'periodo'              => 'required|string|max:20',
            'fecha_inicio_periodo' => 'required|date',
            'fecha_fin_periodo'    => 'required|date|after_or_equal:fecha_inicio_periodo',
            'toneladas_comunes'    => 'required|numeric|min:0',
            'toneladas_reciclables'=> 'required|numeric|min:0',
            'toneladas_peligrosas' => 'required|numeric|min:0',
            'manejadora_rif'       => 'nullable|string|max:15',
            'manifiestos.*'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($request, $validated, $empresa, $user) {
            $archivosGuardados = [];

            if ($request->hasFile('manifiestos')) {
                foreach ($request->file('manifiestos') as $file) {
                    $path = $file->store("declaraciones/{$empresa->id}", 'public');
                    $archivosGuardados[] = [
                        'nombre_original' => $file->getClientOriginalName(),
                        'path'            => $path,
                        'mime_type'       => $file->getClientMimeType(),
                    ];
                }
            }

            DeclaracionResiduo::create([
                'empresa_id'           => $empresa->id,
                'user_id'              => $user->id,
                'periodo'              => $validated['periodo'],
                'fecha_inicio_periodo' => $validated['fecha_inicio_periodo'],
                'fecha_fin_periodo'    => $validated['fecha_fin_periodo'],
                'toneladas_comunes'    => $validated['toneladas_comunes'],
                'toneladas_reciclables'=> $validated['toneladas_reciclables'],
                'toneladas_peligrosas' => $validated['toneladas_peligrosas'],
                'manejadora_rif'       => $validated['manejadora_rif'] ?? null,
                'manifiestos_archivos' => $archivosGuardados,
                'estatus'              => 'PRESENTADA',
            ]);
        });

        return redirect()->route('ciudadano.declaraciones.index')
            ->with('success', 'Declaración de residuos presentada exitosamente.');
    }
}