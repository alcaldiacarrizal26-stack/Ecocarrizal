<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_inspeccion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_orden', 30)->unique(); 
            
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('director_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tipo_inspeccion', 30)->default('ORDINARIA'); 
            $table->text('base_legal_inspeccion');
            $table->text('observaciones_campo'); 
            $table->text('orden_adjunta_path')->nullable();

            $table->timestampTz('fecha_visita_programada');
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            $table->timestampTz('fecha_inicio_inspeccion')->nullable();
            $table->timestampTz('fecha_cierre')->nullable();

            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE ordenes_inspeccion 
                ADD COLUMN estatus estado_orden_inspeccion_enum NOT NULL DEFAULT 'inspector_asignado';
            ");
        } else {
            Schema::table('ordenes_inspeccion', function (Blueprint $table) {
                $table->enum('estatus', ['pendiente', 'inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion', 'atendido', 'desestimado'])->default('inspector_asignado');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_inspeccion');
    }
};