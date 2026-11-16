<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('rif', 15)->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('numero_cuenta_patente')->unique();
            $table->string('correo_comercial');
            $table->string('telefono_comercial');
            
            $table->string('comuna');
            $table->string('sector');
            $table->string('calle_avenida');
            $table->string('edificio_galpon_local');
            $table->text('punto_referencia')->nullable();

            $table->enum('estatus', [
                'PENDIENTE_APROBACION', 
                'OBSERVADO', 
                'APROBADO', 
                'RECHAZADO'
            ])->default('PENDIENTE_APROBACION');

            $table->text('observaciones_fiscalia')->nullable();
            $table->timestamps();

            $table->index('estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};