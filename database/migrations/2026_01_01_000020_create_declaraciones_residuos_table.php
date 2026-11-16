<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('declaraciones_residuos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');

            $table->string('rol_declarante', 20)->default('GENERADOR'); 
            $table->string('periodo', 20);
            $table->date('fecha_inicio_periodo');
            $table->date('fecha_fin_periodo');

            $table->decimal('toneladas_comunes', 12, 3)->default(0.000);
            $table->decimal('toneladas_reciclables', 12, 3)->default(0.000);
            $table->decimal('toneladas_peligrosas', 12, 3)->default(0.000);

            $table->string('manejadora_rif', 15)->nullable();
            $table->string('manejadora_nombre')->nullable();

            $table->string('generador_origen_rif', 15)->nullable();
            $table->string('generador_origen_nombre')->nullable();
            $table->string('hoja_seguimiento_numero', 50)->nullable(); 
            $table->string('vehiculo_placa', 20)->nullable();
            $table->string('destino_final_nombre')->nullable();

            $table->jsonb('manifiestos_archivos')->nullable();
            $table->text('observaciones_fiscalia')->nullable();
            $table->timestampsTz();

            $table->unique(['empresa_id', 'periodo']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE declaraciones_residuos 
                ADD COLUMN estatus estatus_declaracion_enum NOT NULL DEFAULT 'PRESENTADA';
            ");
        } else {
            Schema::table('declaraciones_residuos', function (Blueprint $table) {
                $table->enum('estatus', ['BORRADOR', 'PRESENTADA', 'OBSERVADA', 'CONFORME'])->default('PRESENTADA');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('declaraciones_residuos');
    }
};