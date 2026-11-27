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
            "estatus_entrega_enum"         => "'PENDIENTE', 'EN_REVISION', 'APROBADO', 'RECHAZADO'",
            "estado_solicitud_enum"        => "'PENDIENTE', 'INCOMPLETA', 'EN_REVISION', 'INSPECTOR_ASIGNADO', 'EN_INSPECCION', 'REQUIERE_REINSPECCION', 'APROBADO', 'RECHAZADO'",
            "estado_orden_inspeccion_enum" => "'PENDIENTE', 'INSPECTOR_ASIGNADO', 'EN_INSPECCION', 'REQUIERE_REINSPECCION', 'ATENDIDO', 'DESESTIMADO'",
            "nivel_bitacora_enum"          => "'INFO', 'WARNING', 'DANGER', 'CRITICAL'",
            "estatus_declaracion_enum"     => "'PRESENTADA', 'OBSERVADA', 'CONFORME', 'APROBADA', 'RECHAZADA'",
            "categoria_residuo_enum"       => "'COMUN', 'RECICLABLE', 'PELIGROSO'",
            "tipo_operacion_manejo_enum"   => "'RECOLECCION_TRANSPORTE', 'ALMACENAMIENTO_TEMPORAL', 'TRATAMIENTO', 'RECICLAJE_VALORIZACION', 'DISPOSICION_FINAL'",
            "gravedad_sancion_enum"        => "'LEVE', 'MEDIA', 'GRAVE'",
            "estatus_sancion_enum"         => "'NOTIFICADA', 'EN_SUBSANACION', 'RESUELTA', 'PAGADA', 'CONDONADA', 'ARCHIVADA'",
            "estado_inspeccion_enum"       => "'PROGRAMADA', 'EN_CURSO', 'FINALIZADA', 'CANCELADA', 'REQUIERE_REINSPECCION'",
            "tipo_requisito_enum"          => "'OBLIGATORIO', 'CONDICIONAL', 'DIFERIDO'",
            "estatus_empresa_enum"         => "'PENDIENTE_APROBACION', 'OBSERVADO', 'APROBADO', 'RECHAZADO'",
            "prioridad_ticket_enum"        => "'BAJA', 'MEDIA', 'ALTA'",
            "estatus_ticket_enum"          => "'ABIERTO', 'EN_PROCESO', 'RESUELTO'",
            "tipo_notificacion_enum"       => "'EXITO', 'INFORMACION', 'ADVERTENCIA', 'ERROR'",
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
                "estatus_compensacion_enum", "estatus_entrega_enum", "tipo_requisito_enum",
                "estado_solicitud_enum", "estado_orden_inspeccion_enum", "nivel_bitacora_enum",
                "estatus_declaracion_enum", "categoria_residuo_enum", "estado_inspeccion_enum",
                "tipo_operacion_manejo_enum", "gravedad_sancion_enum", "estatus_sancion_enum",
                "estatus_empresa_enum", "prioridad_ticket_enum", "estatus_ticket_enum", "tipo_notificacion_enum"
            ];

            foreach ($enums as $name) {
                DB::statement("DROP TYPE IF EXISTS {$name} CASCADE;");
            }
        }
    }
};