<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compensaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_compensacion', 30)->unique();
            
            $table->foreignUuid('solicitud_id')->nullable()->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();

            $table->integer('cantidad_asignada')->default(0);
            $table->integer('cantidad_entregada')->default(0);

            $table->timestampTz('fecha_limite')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE compensaciones 
                ADD COLUMN estatus estatus_compensacion_enum NOT NULL DEFAULT 'PENDIENTE';
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('compensaciones');
    }
};