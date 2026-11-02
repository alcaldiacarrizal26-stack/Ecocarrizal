<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets_soporte', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('asunto');
            $table->text('mensaje');
            $table->enum('prioridad', ['BAJA', 'MEDIA', 'ALTA'])->default('MEDIA');
            $table->text('justificacion_alta')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->enum('estatus', ['ABIERTO', 'EN_PROCESO', 'RESUELTO'])->default('ABIERTO');
            $table->text('respuesta_admin')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets_soporte');
    }
};