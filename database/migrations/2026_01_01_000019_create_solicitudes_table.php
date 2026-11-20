<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_solicitud')->unique();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('inspector_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('comuna');
            $table->string('sector');
            $table->text('direccion_exacta');
            $table->text('motivo_descripcion');
            $table->jsonb('datos_especificos')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->timestampTz('fecha_limite_documentos')->nullable();
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            // fecha_inicio_inspeccion eliminada por redundancia (vive en inspecciones)
            $table->timestampTz('fecha_cierre')->nullable();
            
            $table->timestampsTz();

            $table->index(['user_id', 'created_at']);
        });

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->foreignId('tipo_tramite_id')
                ->constrained('tipos_tramites')
                ->restrictOnDelete();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE solicitudes 
                ADD COLUMN estado estado_solicitud_enum NOT NULL DEFAULT 'PENDIENTE';
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};