<?php

namespace App\Http\Controllers\Ciudadano;

use App\Http\Controllers\Controller;
use App\Services\TerritorioService;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    /**
     * Muestra el listado de solicitudes del ciudadano autenticado y la data de soporte.
     */
    public function index()
    {
        // Obtenemos la estructura territorial oficial desde el Service
        $territorios = TerritorioService::getTerritorios();

        // Tipos de trámites disponibles para solicitudes ambientales / gestión de residuos
        $tiposTramites = [
            'declaracion_residuos' => [
                'nombre' => 'Declaración Jurada de Generación de Residuos y Desechos Sólidos',
                'descripcion' => 'Registro y estimación del volumen de generación de residuos para establecimientos comerciales e industriales.',
                'requisitos' => [
                    'Registro Mercantil y última acta de asamblea',
                    'RIF de la empresa o firma personal (Vigente)',
                    'Cédula de identidad del representante legal',
                    'Patente de Industria y Comercio / Licencia de Actividades Económicas',
                    'Fotografías del área de almacenamiento de residuos',
                    'Cálculo/Estimación de volumen de desechos semanales',
                ]
            ],
            'permiso_transporte_residuos' => [
                'nombre' => 'Permiso de Transporte y Disposición Final de Residuos',
                'descripcion' => 'Autorización para vehículos dedicados al traslado de materiales aprovechables o desechos en el municipio.',
                'requisitos' => [
                    'RIF de la empresa prestadora del servicio',
                    'Cédula de identidad del conductor o representante',
                    'Título de propiedad / Carnet de circulación del vehículo',
                    'Póliza de seguro de responsabilidad civil (Vigente)',
                    'Certificado de revisión técnica/mecánica del vehículo',
                    'Ruta de transporte proyectada y destino final autorizado',
                ]
            ],
            'poda_tala' => [
                'nombre' => 'Autorización para Poda o Tala Fitosanitaria / Preventiva',
                'descripcion' => 'Evaluación e inspección técnica de árboles en propiedad privada o espacio público que representen riesgo o afectación.',
                'requisitos' => [
                    'Cédula de identidad del solicitante',
                    'Documento de propiedad del inmueble o constancia de residencia',
                    'Fotografías claras de la especie vegetal y la condición de riesgo',
                    'Carta explicativa con el motivo de la solicitud',
                ]
            ],
            'inspeccion_riesgo_ambiental' => [
                'nombre' => 'Solicitud de Inspección de Riesgo Ambiental o Denuncia',
                'descripcion' => 'Atención de emergencias o afectaciones por vertidos, quema ilegal, acumulación indebida de desechos o ruidos molestos.',
                'requisitos' => [
                    'Cédula de identidad del denunciante o solicitante',
                    'Ubicación exacta y referencias del lugar afectado',
                    'Fotografías o evidencia digital del problema ambiental',
                    'Exposición breve de los hechos y horario de ocurrencia',
                ]
            ],
        ];

        // NOTA: Reemplazar por la consulta real cuando integres la tabla de solicitudes (Ej: Solicitud::where('user_id', auth()->id())->latest()->get())
        $tramites = collect([]);

        return view('ciudadano.solicitudes', compact('tramites', 'territorios', 'tiposTramites'));
    }

    /**
     * Almacena una nueva solicitud.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tipo_tramite'     => 'required|string',
            'comuna'           => 'required|string',
            'sector'           => 'required|string',
            'direccion_exacta' => 'required|string|max:500',
            'motivo_solicitud' => 'required|string|max:1000',
            'documentos'       => 'required|array|min:1',
            'documentos.*'     => 'file|mimes:pdf,jpg,jpeg,png|max:10240', // Máx 10MB por archivo
        ]);

        // Lógica de almacenamiento, guardado de archivos e inserción en BD...

        return redirect()
            ->route('ciudadano.solicitudes.index')
            ->with('success', 'Tu solicitud ha sido registrada exitosamente.');
    }
}