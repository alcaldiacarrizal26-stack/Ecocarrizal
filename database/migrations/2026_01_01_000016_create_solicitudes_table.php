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
            $table->string('codigo_expediente')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inspector_id')->nullable()->constrained('users')->onDelete('set null');

            $table->string('comuna');
            $table->string('sector');
            $table->text('direccion_exacta');
            $table->text('motivo_descripcion');
            $table->jsonb('datos_especificos')->nullable();
            $table->text('observaciones')->nullable();
            
            $table->timestampTz('fecha_limite_documentos')->nullable();
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            $table->timestampTz('fecha_inicio_inspeccion')->nullable();
            $table->timestampTz('fecha_cierre')->nullable();
            
            $table->timestampsTz();

            $table->index(['user_id', 'created_at']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE solicitudes 
                ADD COLUMN tipo_tramite tipo_tramite_enum NOT NULL,
                ADD COLUMN estado estado_solicitud_enum NOT NULL DEFAULT 'pendiente';
            ");
        } else {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->enum('tipo_tramite', ['inspeccion', 'poda_deshierbe', 'desmalezamiento_conformacion', 'tala']);
                $table->enum('estado', ['pendiente', 'incompleta', 'en_revision', 'inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion', 'aprobado', 'rechazado'])->default('pendiente');
            });
        }

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};