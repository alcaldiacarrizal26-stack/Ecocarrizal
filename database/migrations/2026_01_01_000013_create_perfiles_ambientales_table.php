<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles_ambientales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();

            $table->string('rol_ambiental')->default('GENERADOR'); 
            $table->string('subtipo_generador')->nullable(); 
            $table->string('numero_racda')->nullable()->unique(); 
            
            $table->boolean('genera_efluentes')->default(false);
            $table->boolean('genera_emisiones')->default(false);
            $table->boolean('genera_peligrosos')->default(false);
            $table->boolean('genera_ruidos')->default(false);
            $table->boolean('usa_pozo_agua')->default(false);

            $table->string('modalidad_manejadora')->nullable(); 
            $table->string('manejadora_contratada')->nullable();
            $table->string('manejadora_rif', 15)->nullable();
            $table->string('manejadora_racda')->nullable();

            $table->string('modalidad_atencion_manejador')->nullable(); 
            $table->string('generador_exclusivo_nombre')->nullable();
            $table->string('generador_exclusivo_rif', 15)->nullable();
            $table->string('generador_exclusivo_racda')->nullable();

            $table->jsonb('actividades_manejador')->nullable();
            $table->text('sustancias_manejadas')->nullable();
            $table->jsonb('residuos_tipos')->nullable();

            $table->jsonb('residuos_comunes')->nullable();
            $table->jsonb('residuos_reciclables')->nullable();
            $table->jsonb('residuos_peligrosos')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles_ambientales');
    }
};