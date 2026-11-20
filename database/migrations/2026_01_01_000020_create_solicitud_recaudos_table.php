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
            $table->foreignUuid('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('recaudo_catalogo_id')
                ->constrained('recaudos_catalogo')
                ->restrictOnDelete();
            $table->string('nombre_archivo');
            $table->text('bucket_path');
            $table->text('archivo_url');
            $table->timestampsTz();

            $table->index('solicitud_id');
            $table->index('recaudo_catalogo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_recaudos');
    }
};