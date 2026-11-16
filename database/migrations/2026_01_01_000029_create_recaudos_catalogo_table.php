<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recaudos_catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_tramite'); // 'inspeccion', 'poda_deshierbe', 'tala', etc.
            $table->string('key');          // 'cedula', 'carta_explicativa', etc.
            $table->string('label');        // Nombre legible del recaudo
            $table->string('tipo_requisito'); // 'obligatorio', 'condicional', 'opcional'
            $table->text('hint')->nullable();// Descripción o pista para el usuario
            $table->timestamps();

            $table->foreign('tipo_tramite')
                  ->references('slug')
                  ->on('tipos_tramites')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recaudos_catalogo');
    }
};