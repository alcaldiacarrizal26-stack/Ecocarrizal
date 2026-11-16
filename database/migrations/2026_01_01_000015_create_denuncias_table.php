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
            $table->string('codigo', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('tipo_contaminacion');
            $table->string('otro_tipo_contaminacion')->nullable();
            
            $table->string('comuna');
            $table->string('sector');
            $table->string('punto_referencia');
            
            $table->enum('nivel_gravedad', ['baja', 'media', 'alta'])->default('baja');
            $table->text('justificacion_gravedad')->nullable();
            
            $table->text('descripcion');
            $table->jsonb('evidencias')->nullable();
            
            $table->enum('estatus', [
                'pendiente', 
                'inspector_asignado', 
                'en_inspeccion', 
                'en_revision',
                'requiere_reinspeccion',
                'atendido', 
                'desestimado'
            ])->default('pendiente');
            $table->text('observaciones')->nullable();
            
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            $table->timestampTz('fecha_inicio_inspeccion')->nullable();
            $table->timestampTz('fecha_cierre')->nullable();

            $table->timestampsTz();

            $table->index('estatus');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('denuncias');
    }
};