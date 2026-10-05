<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Models\DeclaracionResiduo;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeclaracionResiduoController extends Controller
{
    /**
     * Muestra el historial de declaraciones de la empresa del usuario.
     */
    public function index()
    {
        $user = Auth::user();
        
        $empresa = Empresa::where('user_id', $user->id)->first();

        if (!$empresa) {
            return redirect()->route('ciudadano.empresas.crear')
                ->with('warning', 'Debes registrar una empresa antes de realizar declaraciones de residuos.');
        }

        $declaraciones = DeclaracionResiduo::with(['detalles'])
            ->where('empresa_id', $empresa->id)
            ->latest()
            ->paginate(10);

        return view('ciudadano.empresa.declaraciones', compact('empresa', 'declaraciones'));
    }

    /**
     * Formulario / Modal para crear una nueva declaración.
     */
    public function create()
    {
        $user = Auth::user();
        $empresa = Empresa::with('perfilAmbiental')->where('user_id', $user->id)->firstOrFail();

        return view('ciudadano.empresa.declarar', compact('empresa'));
    }

    /**
     * Guarda la declaración mensual/trimestral con sus detalles en BD.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $empresa = Empresa::with('perfilAmbiental')->where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'periodo'              => 'required|string|max:20',
            'frecuencia'           => 'required|in:MENSUAL,TRIMESTRAL,SEMESTRAL,ANUAL',
            'fecha_inicio_periodo' => 'required|date',
            'fecha_fin_periodo'    => 'required|date|after_or_equal:fecha_inicio_periodo',
            'manejadora_rif'       => 'nullable|string|max:15',
            'manejadora_nombre'    => 'nullable|string|max:255',
            'manifiestos.*'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Ítems de la declaración
            'detalles'                      => 'required|array|min:1',
            'detalles.*.codigo_residuo'     => 'required|string',
            'detalles.*.nombre_residuo'     => 'required|string',
            'detalles.*.categoria'          => 'required|in:COMUN,RECICLABLE,PELIGROSO',
            'detalles.*.cantidad'           => 'required|numeric|min:0.001',
            'detalles.*.unidad_medida'      => 'required|in:TON,KG,M3,LITROS',
            'detalles.*.manifiesto'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::transaction(function () use ($request, $validated, $empresa, $user) {
            // 1. Guardar manifiestos generales
            $archivosGuardados = [];
            if ($request->hasFile('manifiestos')) {
                foreach ($request->file('manifiestos') as $file) {
                    $path = $file->store("declaraciones/{$empresa->id}/generales", 'private');
                    $archivosGuardados[] = [
                        'nombre_original' => $file->getClientOriginalName(),
                        'path'            => $path,
                        'mime_type'       => $file->getClientMimeType(),
                    ];
                }
            }

            // 2. Acumuladores para los totales de la cabecera
            $tonComunes = 0.000;
            $tonReciclables = 0.000;
            $tonPeligrosas = 0.000;

            // 3. Crear la declaración cabecera
            $declaracion = DeclaracionResiduo::create([
                'empresa_id'           => $empresa->id,
                'user_id'              => $user->id,
                'periodo'              => $validated['periodo'],
                'frecuencia'           => $validated['frecuencia'],
                'fecha_inicio_periodo' => $validated['fecha_inicio_periodo'],
                'fecha_fin_periodo'    => $validated['fecha_fin_periodo'],
                'toneladas_comunes'    => 0,
                'toneladas_reciclables'=> 0,
                'toneladas_peligrosas' => 0,
                'manejadora_rif'       => $validated['manejadora_rif'] ?? null,
                'manejadora_nombre'    => $validated['manejadora_nombre'] ?? null,
                'manifiestos_archivos' => $archivosGuardados,
                'estatus'              => 'PRESENTADA',
            ]);

            // 4. Recorrer y registrar cada renglón de detalle
            foreach ($validated['detalles'] as $index => $item) {
                // Factor de conversión a toneladas estándar
                $factorTon = match ($item['unidad_medida']) {
                    'TON'    => 1.0,
                    'KG'     => 0.001,
                    'LITROS' => 0.001, // Estimación estándar agua/densidad 1
                    'M3'     => 1.0,   // Densidad promedio referencia
                    default  => 0.001,
                };

                $cantidadTon = round((float)$item['cantidad'] * $factorTon, 3);

                // Acumular segun categoría
                if ($item['categoria'] === 'COMUN') {
                    $tonComunes += $cantidadTon;
                } elseif ($item['categoria'] === 'RECICLABLE') {
                    $tonReciclables += $cantidadTon;
                } elseif ($item['categoria'] === 'PELIGROSO') {
                    $tonPeligrosas += $cantidadTon;
                }

                // Subir manifiesto específico del detalle si existe
                $manifiestoDetallePath = null;
                if ($request->hasFile("detalles.{$index}.manifiesto")) {
                    $fileDetalle = $request->file("detalles.{$index}.manifiesto");
                    $manifiestoDetallePath = $fileDetalle->store("declaraciones/{$empresa->id}/detalles", 'private');
                }

                $declaracion->detalles()->create([
                    'codigo_residuo'     => $item['codigo_residuo'],
                    'nombre_residuo'     => $item['nombre_residuo'],
                    'categoria'          => $item['categoria'],
                    'cantidad'           => $item['cantidad'],
                    'unidad_medida'      => $item['unidad_medida'],
                    'cantidad_toneladas' => $cantidadTon,
                    'manifiesto_path'    => $manifiestoDetallePath,
                ]);
            }

            // 5. Actualizar la cabecera con las toneladas totales calculadas
            $declaracion->update([
                'toneladas_comunes'    => $tonComunes,
                'toneladas_reciclables'=> $tonReciclables,
                'toneladas_peligrosas' => $tonPeligrosas,
            ]);
        });

        return redirect()->route('ciudadano.declaraciones.index')
            ->with('success', 'Declaración de residuos presentada exitosamente.');
    }
}