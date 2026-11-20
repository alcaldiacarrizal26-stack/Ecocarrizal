<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_sancion', 30)->unique();
            
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignUuid('director_id')->constrained('users'); // Corregido a foreignUuid
            $table->foreignUuid('inspeccion_id')->nullable()->constrained('inspecciones')->nullOnDelete(); 

            $table->string('tipo_infraccion'); 
            $table->text('motivo_descripcion');
            $table->text('base_legal')->nullable(); 

            $table->decimal('unidades_tributarias', 10, 2)->default(0.00);

            $table->date('fecha_incidencia');
            $table->date('fecha_citacion')->nullable();
            $table->date('fecha_limite_subsanacion')->nullable();

            $table->jsonb('evidencias_fiscalia')->nullable(); 
            $table->jsonb('evidencias_subsanacion')->nullable(); 
            $table->text('observaciones_subsanacion')->nullable(); 
            $table->text('observaciones_resolucion')->nullable(); 

            $table->timestampTz('fecha_resolucion')->nullable();
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE sanciones 
                ADD COLUMN gravedad gravedad_sancion_enum NOT NULL DEFAULT 'LEVE',
                ADD COLUMN estatus estatus_sancion_enum NOT NULL DEFAULT 'NOTIFICADA';
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};