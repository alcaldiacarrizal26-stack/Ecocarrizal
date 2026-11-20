<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('denuncias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_denuncia', 20)->unique();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('inspector_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tipo_contaminacion');
            $table->string('otro_tipo_contaminacion')->nullable();
            
            $table->string('comuna');
            $table->string('sector');
            $table->string('punto_referencia');
            
            $table->enum('nivel_gravedad', ['BAJA', 'MEDIA', 'ALTA', 'CRITICA'])->default('BAJA');
            $table->text('justificacion_gravedad')->nullable();
            
            $table->text('descripcion');
            $table->jsonb('evidencias')->nullable();
            
            $table->enum('estado', [
                'PENDIENTE', 
                'INSPECTOR_ASIGNADO', 
                'EN_INSPECCION', 
                'EN_REVISION',
                'REQUIERE_REINSPECCION',
                'ATENDIDO', 
                'DESESTIMADO'
            ])->default('PENDIENTE');
            
            $table->text('observaciones')->nullable();
            
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            // fecha_inicio_inspeccion eliminada por redundancia
            $table->timestampTz('fecha_cierre')->nullable();

            $table->timestampsTz();

            $table->index('estado');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};