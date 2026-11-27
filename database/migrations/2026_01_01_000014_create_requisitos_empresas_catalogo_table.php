<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('requisitos_empresas_catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            $table->string('aplica_a_rol', 50)->default('TODOS')
                ->comment('TODOS, GENERADOR, MANEJADOR, NO_APLICA o combinaciones separadas por coma');

            $table->string('tipo_requisito', 20)->default('OBLIGATORIO')
                ->comment('OBLIGATORIO, CONDICIONAL, OPCIONAL');

            $table->string('condicion_flag', 50)->nullable()
                ->comment('genera_peligrosos, genera_efluentes, genera_emisiones, genera_riesgo_ambiental, usa_pozo_agua, modalidad_externa, es_persona_juridica');

            $table->boolean('es_vitalicio_default')->default(false);
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestampsTz();

            $table->unique(['codigo', 'aplica_a_rol'], 'requisitos_codigo_rol_unique');
            $table->index('activo');
            $table->index('aplica_a_rol');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requisitos_empresas_catalogo');
    }
};