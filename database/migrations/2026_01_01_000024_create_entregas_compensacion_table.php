<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entregas_compensacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('compensacion_id')->constrained('compensaciones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->integer('cantidad_entregada');
            $table->string('especie_entregada');
            $table->text('comprobante_path');
            $table->text('observacion_ciudadano')->nullable();
            $table->text('observacion_director')->nullable();
            $table->timestampTz('fecha_revision')->nullable();

            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE entregas_compensacion 
                ADD COLUMN estatus estatus_entrega_enum NOT NULL DEFAULT 'PENDIENTE';
            ");
        } else {
            Schema::table('entregas_compensacion', function (Blueprint $table) {
                $table->enum('estatus', ['PENDIENTE', 'APROBADO', 'RECHAZADO'])->default('PENDIENTE');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('entregas_compensacion');
    }
};