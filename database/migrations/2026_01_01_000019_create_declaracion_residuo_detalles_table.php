<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaracion_residuo_detalles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('declaracion_id')->constrained('declaraciones_residuos')->cascadeOnDelete();

            $table->string('codigo_residuo', 100);
            $table->string('nombre_residuo');
            $table->decimal('cantidad', 12, 3);
            $table->string('unidad_medida', 10)->default('TON');
            $table->decimal('cantidad_toneladas', 12, 3);

            $table->string('manifiesto_path')->nullable();
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE declaracion_residuo_detalles 
                ADD COLUMN categoria categoria_residuo_enum NOT NULL DEFAULT 'COMUN',
                ADD COLUMN tipo_operacion tipo_operacion_manejo_enum NULL;
            ");
        } else {
            Schema::table('declaracion_residuo_detalles', function (Blueprint $table) {
                $table->enum('categoria', ['COMUN', 'RECICLABLE', 'PELIGROSO'])->default('COMUN');
                $table->enum('tipo_operacion', [
                    'RECOLECCION_TRANSPORTE',
                    'ALMACENAMIENTO_TEMPORAL',
                    'TRATAMIENTO',
                    'RECICLAJE_VALORIZACION',
                    'DISPOSICION_FINAL'
                ])->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('declaracion_residuo_detalles');
    }
};