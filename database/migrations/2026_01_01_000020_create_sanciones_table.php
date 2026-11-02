<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_expediente', 30)->unique(); 
            
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('director_id')->constrained('users'); 
            $table->foreignUuid('inspeccion_id')->nullable()->constrained('inspecciones')->nullOnDelete(); 

            $table->string('tipo_infraccion'); 
            $table->enum('gravedad', ['LEVE', 'GRAVE', 'MUY_GRAVE'])->default('LEVE');
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

            $table->enum('estatus', [
                'PENDIENTE_NOTIFICACION',
                'NOTIFICADA',
                'EN_REVISION',
                'SUBSANADA',
                'DESESTIMADA'
            ])->default('PENDIENTE_NOTIFICACION');

            $table->timestampTz('fecha_resolucion')->nullable();
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanciones');
    }
};