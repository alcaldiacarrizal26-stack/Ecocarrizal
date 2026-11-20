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
            
            $table->foreignId('requisito_id')
                ->constrained('requisitos_empresas_catalogo')
                ->restrictOnDelete();
            
            $table->string('numero_folio')->nullable();
            $table->text('archivo_path')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('es_vitalicio')->default(false);
            $table->text('observaciones')->nullable();
            
            // Auditoría de revisión (faltaba)
            $table->foreignUuid('revisado_por')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestampTz('fecha_revision')->nullable();
            
            $table->timestampsTz();

            $table->unique(['empresa_id', 'requisito_id']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE documentos_expediente 
                ADD COLUMN estado_revision estado_revision_doc_enum NOT NULL DEFAULT 'OBSERVADO';
            ");
            DB::statement("
                ALTER TABLE documentos_expediente 
                ADD CONSTRAINT chk_vitalicio_fecha CHECK (
                    (es_vitalicio = true AND fecha_vencimiento IS NULL)
                    OR (es_vitalicio = false AND fecha_vencimiento IS NOT NULL)
                );
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_expediente');
    }
};