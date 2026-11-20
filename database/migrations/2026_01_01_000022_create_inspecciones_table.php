<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_acta', 30)->unique();
            $table->string('tipo_inspeccion', 30)->default('ORDINARIA'); 
            
            $table->foreignUuid('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
            $table->foreignUuid('denuncia_id')->nullable()->constrained('denuncias')->nullOnDelete();
            $table->foreignUuid('orden_inspeccion_id')->nullable()->constrained('ordenes_inspeccion')->nullOnDelete();
            
            $table->foreignUuid('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();

            $table->timestampTz('fecha_visita');
            $table->text('observaciones_campo')->nullable();
            $table->jsonb('hallazgos')->nullable();
            $table->jsonb('fotos_evidencia')->nullable();
            $table->text('recomendaciones')->nullable();
            
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            // Enum de estado de inspección (creado defensivamente si no está en 000000)
            DB::statement("
                DO $$ BEGIN
                    CREATE TYPE estado_inspeccion_enum AS ENUM (
                        'PROGRAMADA','EN_CURSO','FINALIZADA','CANCELADA','REQUIERE_REINSPECCION'
                    );
                EXCEPTION WHEN duplicate_object THEN NULL;
                END $$;
            ");

            // CHECK: exactamente uno de los tres orígenes
            DB::statement("
                ALTER TABLE inspecciones 
                ADD CONSTRAINT chk_origen_unico CHECK (
                    (solicitud_id IS NOT NULL)::int +
                    (denuncia_id IS NOT NULL)::int +
                    (orden_inspeccion_id IS NOT NULL)::int = 1
                );
            ");

            // Columnas adicionales con enums nativos y fechas de ciclo
            DB::statement("
                ALTER TABLE inspecciones 
                ADD COLUMN dictamen dictamen_inspeccion_enum NULL,
                ADD COLUMN estado estado_inspeccion_enum NOT NULL DEFAULT 'PROGRAMADA',
                ADD COLUMN fecha_inicio timestamptz NULL,
                ADD COLUMN fecha_cierre timestamptz NULL,
                ADD COLUMN version int4 NOT NULL DEFAULT 1;
            ");

            // CHECK: coherencia estado <-> fechas
            DB::statement("
                ALTER TABLE inspecciones 
                ADD CONSTRAINT chk_estado_fechas CHECK (
                    (estado = 'EN_CURSO' AND fecha_inicio IS NOT NULL AND fecha_cierre IS NULL)
                    OR (estado IN ('FINALIZADA','CANCELADA') AND fecha_inicio IS NOT NULL AND fecha_cierre IS NOT NULL)
                    OR (estado IN ('PROGRAMADA','REQUIERE_REINSPECCION') AND fecha_cierre IS NULL)
                );
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
        
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP TYPE IF EXISTS estado_inspeccion_enum CASCADE;");
        }
    }
};