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
     * Guarda la declaración mensual/trimestral/anual con sus detalles en BD.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $empresa = Empresa::with('perfilAmbiental')->where('user_id', $user->id)->firstOrFail();

        $rolDeclarante = $request->input('rol_declarante', 'GENERADOR');
        $isNoAplica = $rolDeclarante === 'NO_APLICA';

        // 1. Reglas de Validación Flexibles según el Rol
        $rules = [
            'rol_declarante'          => 'required|in:GENERADOR,MANEJADOR,NO_APLICA',
            'periodo'                 => 'required|string|max:20',
            'frecuencia'              => 'required|in:SEMANAL',
            'fecha_inicio_periodo'    => 'required|date',
            'fecha_fin_periodo'       => $isNoAplica ? 'nullable|date' : 'required|date|after_or_equal:fecha_inicio_periodo',
            
            // Campos de Trazabilidad / Manejador o Generador
            'manejadora_rif'          => 'nullable|string|max:15',
            'manejadora_nombre'       => 'nullable|string|max:255',
            'generador_origen_nombre' => 'nullable|string|max:255',
            'generador_origen_rif'    => 'nullable|string|max:15',
            'hoja_seguimiento_numero' => 'nullable|string|max:50',
            'vehiculo_placa'          => 'nullable|string|max:20',
            'destino_final_nombre'    => 'nullable|string|max:255',
            'declaracion_jurada'      => $isNoAplica ? 'required|accepted' : 'nullable',

            // Anexos Generales
            'manifiestos'             => 'nullable|array',
            'manifiestos.*'           => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];

        // Validar renglones de detalles únicamente si NO es NO_APLICA
        if (!$isNoAplica) {
            $rules['detalles']                  = 'required|array|min:1';
            $rules['detalles.*.codigo_residuo'] = 'required|string';
            $rules['detalles.*.nombre_residuo'] = 'required|string';
            $rules['detalles.*.categoria']      = 'required|in:COMUN,RECICLABLE,PELIGROSO';
            $rules['detalles.*.tipo_operacion'] = 'nullable|string';
            $rules['detalles.*.cantidad']       = 'required|numeric|min:0';
            $rules['detalles.*.unidad_medida']  = 'required|in:TON,KG,M3,LITROS';
            $rules['detalles.*.manifiesto']     = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        $validated = $request->validate($rules);

        // Mantenemos un registro de archivos físicamente guardados para limpieza si falla la transacción
        $archivosProcesados = [];

        try {
            DB::transaction(function () use ($request, $validated, $empresa, $user, $isNoAplica, $rolDeclarante, &$archivosProcesados) {
                
                // A. Procesar manifiestos generales
                $archivosGuardados = [];
                if ($request->hasFile('manifiestos')) {
                    foreach ($request->file('manifiestos') as $file) {
                        $path = $file->store("declaraciones/{$empresa->id}/generales", 'private');
                        
                        $archivosProcesados[] = $path;
                        
                        $archivosGuardados[] = [
                            'nombre_original' => $file->getClientOriginalName(),
                            'path'            => $path,
                            'mime_type'       => $file->getClientMimeType(),
                        ];
                    }
                }

                $tonComunes = 0.000;
                $tonReciclables = 0.000;
                $tonPeligrosas = 0.000;
                $detallesAInsertar = [];

                // B. Pre-calcular acumulados de toneladas y preparar los detalles (si aplica)
                if (!$isNoAplica && isset($validated['detalles'])) {
                    foreach ($validated['detalles'] as $index => $item) {
                        // Factor de conversión estandarizado a toneladas
                        $factorTon = match ($item['unidad_medida']) {
                            'TON'    => 1.0,
                            'KG'     => 0.001,
                            'LITROS' => 0.001,
                            'M3'     => 1.0,
                            default  => 0.001,
                        };

                        $cantidadTon = round((float)$item['cantidad'] * $factorTon, 3);

                        // Acumular según categoría
                        match ($item['categoria']) {
                            'COMUN'      => $tonComunes += $cantidadTon,
                            'RECICLABLE' => $tonReciclables += $cantidadTon,
                            'PELIGROSO'  => $tonPeligrosas += $cantidadTon,
                        };

                        // Subir manifiesto específico del renglón si fue cargado
                        $manifiestoDetallePath = null;
                        if ($request->hasFile("detalles.{$index}.manifiesto")) {
                            $fileDetalle = $request->file("detalles.{$index}.manifiesto");
                            $manifiestoDetallePath = $fileDetalle->store("declaraciones/{$empresa->id}/detalles", 'private');
                            
                            $archivosProcesados[] = $manifiestoDetallePath;
                        }

                        $detallesAInsertar[] = [
                            'codigo_residuo'     => $item['codigo_residuo'],
                            'nombre_residuo'     => $item['nombre_residuo'],
                            'categoria'          => $item['categoria'],
                            'tipo_operacion'     => $item['tipo_operacion'] ?? null,
                            'cantidad'           => $item['cantidad'],
                            'unidad_medida'      => $item['unidad_medida'],
                            'cantidad_toneladas' => $cantidadTon,
                            'manifiesto_path'    => $manifiestoDetallePath,
                        ];
                    }
                }

                // C. Crear la cabecera directamente con todos sus metadatos
                $declaracion = DeclaracionResiduo::create([
                    'empresa_id'              => $empresa->id,
                    'user_id'                 => $user->id,
                    'rol_declarante'          => $rolDeclarante,
                    'periodo'                 => $validated['periodo'],
                    'frecuencia'              => $validated['frecuencia'],
                    'fecha_inicio_periodo'    => $validated['fecha_inicio_periodo'],
                    'fecha_fin_periodo'       => $validated['fecha_fin_periodo'] ?? $validated['fecha_inicio_periodo'],
                    'toneladas_comunes'       => $tonComunes,
                    'toneladas_reciclables'   => $tonReciclables,
                    'toneladas_peligrosas'    => $tonPeligrosas,
                    
                    // Campos opcionales / Trazabilidad
                    'manejadora_rif'          => $validated['manejadora_rif'] ?? null,
                    'manejadora_nombre'       => $validated['manejadora_nombre'] ?? null,
                    'generador_origen_nombre' => $validated['generador_origen_nombre'] ?? null,
                    'generador_origen_rif'    => $validated['generador_origen_rif'] ?? null,
                    'hoja_seguimiento_numero' => $validated['hoja_seguimiento_numero'] ?? null,
                    'vehiculo_placa'          => $validated['vehiculo_placa'] ?? null,
                    'destino_final_nombre'    => $validated['destino_final_nombre'] ?? null,
                    
                    'manifiestos_archivos'    => $archivosGuardados,
                    'estatus'                 => 'PRESENTADA',
                ]);

                // D. Registrar los renglones de detalle mediante relación
                if (!empty($detallesAInsertar)) {
                    $declaracion->detalles()->createMany($detallesAInsertar);
                }
            });

        } catch (\Throwable $e) {
            // Si la BD falla, revertir y limpiar los archivos físicos guardados en disco
            foreach ($archivosProcesados as $path) {
                Storage::disk('private')->delete($path);
            }

            return back()->withInput()->with('error', 'Ocurrió un error al procesar la declaración: ' . $e->getMessage());
        }

        $mensajeExito = $isNoAplica 
            ? 'Notificación simplificada registrada exitosamente.' 
            : 'Declaración de residuos presentada exitosamente.';

        return redirect()->route('ciudadano.declaraciones.index')
            ->with('success', $mensajeExito);
    }
}