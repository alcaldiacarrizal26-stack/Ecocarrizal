<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_expediente', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();

            $table->string('codigo_requisito');
            $table->text('nombre_requisito');
            $table->string('numero_folio')->nullable();
            $table->text('archivo_path')->nullable();
            
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('es_vitalicio')->default(false);

            $table->text('observaciones')->nullable();
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE documentos_expediente 
                ADD COLUMN estado_revision estado_revision_doc_enum NOT NULL DEFAULT 'VALIDO';
            ");
        } else {
            Schema::table('documentos_expediente', function (Blueprint $table) {
                $table->enum('estado_revision', ['VALIDO', 'OBSERVADO', 'RECHAZADO'])->default('VALIDO');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_expediente');
    }
};