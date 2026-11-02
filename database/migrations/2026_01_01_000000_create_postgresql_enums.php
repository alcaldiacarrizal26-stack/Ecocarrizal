<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $enums = [
            "tipo_racda_enum"              => "'NO_APLICA', 'GENERADOR', 'MANEJADOR', 'TRANSPORTISTA'",
            "estado_revision_doc_enum"     => "'VALIDO', 'OBSERVADO', 'RECHAZADO'",
            "dictamen_inspeccion_enum"     => "'APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'",
            "estatus_compensacion_enum"    => "'PENDIENTE', 'PARCIAL', 'COMPLETADO'",
            "estatus_entrega_enum"         => "'PENDIENTE', 'APROBADO', 'RECHAZADO'",
            "tipo_tramite_enum"            => "'inspeccion', 'poda_deshierbe', 'desmalezamiento_conformacion', 'tala'",
            "estado_solicitud_enum"        => "'pendiente', 'incompleta', 'en_revision', 'inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion', 'aprobado', 'rechazado'",
            "estado_orden_inspeccion_enum" => "'pendiente', 'inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion', 'atendido', 'desestimado'",
            "nivel_bitacora_enum"          => "'info', 'warning', 'danger', 'critical'",
            "estatus_declaracion_enum"     => "'PRESENTADA', 'OBSERVADA', 'CONFORME', 'APROBADA', 'RECHAZADA'",
            "frecuencia_declaracion_enum"  => "'MENSUAL', 'TRIMESTRAL', 'SEMESTRAL', 'ANUAL', 'SEMANAL'",
            "categoria_residuo_enum"       => "'COMUN', 'RECICLABLE', 'PELIGROSO'",
            "tipo_operacion_manejo_enum"   => "'RECOLECCION_TRANSPORTE', 'ALMACENAMIENTO_TEMPORAL', 'TRATAMIENTO', 'RECICLAJE_VALORIZACION', 'DISPOSICION_FINAL'",
        ];

        if (DB::getDriverName() === 'pgsql') {
            foreach ($enums as $name => $values) {
                DB::statement("DROP TYPE IF EXISTS {$name} CASCADE;");
                DB::statement("CREATE TYPE {$name} AS ENUM ({$values});");
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            $enums = [
                "tipo_racda_enum", "estado_revision_doc_enum", "dictamen_inspeccion_enum",
                "estatus_compensacion_enum", "estatus_entrega_enum", "tipo_tramite_enum",
                "estado_solicitud_enum", "estado_orden_inspeccion_enum", "nivel_bitacora_enum",
                "estatus_declaracion_enum", "frecuencia_declaracion_enum", "categoria_residuo_enum",
                "tipo_operacion_manejo_enum"
            ];

            foreach ($enums as $name) {
                DB::statement("DROP TYPE IF EXISTS {$name} CASCADE;");
            }
        }
    }
};