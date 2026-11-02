<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_recaudos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
            $table->string('tipo_recaudo');
            $table->string('nombre_archivo');
            $table->string('bucket_path');
            $table->string('archivo_url');
            $table->boolean('es_evidencia_fotografica')->default(false);
            
            $table->timestampsTz();

            $table->index('solicitud_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_recaudos');
    }
};