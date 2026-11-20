<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_soporte', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto');
            $table->timestampTz('fecha_resolucion')->nullable();
            $table->timestampsTz();
            $table->index('user_id');
        });

        DB::statement("
        ALTER TABLE tickets_soporte
            ADD COLUMN prioridad prioridad_ticket_enum NOT NULL DEFAULT 'MEDIA',
            ADD COLUMN estatus estatus_ticket_enum NOT NULL DEFAULT 'ABIERTO',
            ADD COLUMN justificacion_alta text NULL,
            ADD CONSTRAINT chk_justif_alta CHECK (prioridad != 'ALTA' OR justificacion_alta IS NOT NULL);
        ");

        Schema::create('ticket_mensajes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ticket_id')->constrained('tickets_soporte')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users');
            $table->boolean('es_admin');
            $table->text('mensaje');
            $table->text('evidencia_path')->nullable();
            $table->timestampTz('editado_at')->nullable();
            $table->timestampsTz();
            $table->index(['ticket_id', 'created_at']);
        });

        DB::statement("ALTER TABLE ticket_mensajes ADD CONSTRAINT chk_mensaje_min_length
        CHECK (length(trim(mensaje)) >= 10)");
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_mensajes');
        Schema::dropIfExists('tickets_soporte');
    }
};