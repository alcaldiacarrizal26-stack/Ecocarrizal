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
            $table->text('base_legal_inspeccion')->nullable();
            $table->text('orden_adjunta_path')->nullable();
            
            $table->foreignUuid('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
            $table->foreignUuid('denuncia_id')->nullable()->constrained('denuncias')->nullOnDelete();
            $table->foreignUuid('orden_inspeccion_id')->nullable()->constrained('ordenes_inspeccion')->nullOnDelete();
            
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();

            $table->timestampTz('fecha_visita');
            $table->text('observaciones_campo');
            $table->jsonb('hallazgos')->nullable();
            $table->jsonb('fotos_evidencia')->nullable();
            $table->text('recomendaciones')->nullable();
            
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE inspecciones 
                ADD COLUMN dictamen dictamen_inspeccion_enum NOT NULL DEFAULT 'CON_OBSERVACIONES';
            ");
        } else {
            Schema::table('inspecciones', function (Blueprint $table) {
                $table->enum('dictamen', ['APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'])->default('CON_OBSERVACIONES');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inspecciones');
    }
};