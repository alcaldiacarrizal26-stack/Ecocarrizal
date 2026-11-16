<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tipos_tramites', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // 'inspeccion', 'poda_deshierbe', etc.
            $table->string('nombre');          // 'Solicitud de Inspección TÉCNICA'
            $table->text('descripcion');       // Descripción detallada
            $table->integer('plazo_dias')->default(0); // Plazo en días
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_tramites');
    }
};