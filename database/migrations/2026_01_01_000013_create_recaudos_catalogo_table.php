<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recaudos_catalogo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_tramite_id')
                ->constrained('tipos_tramites')
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('label');
            $table->string('tipo_requisito');
            $table->string('tipo_documento')->default('FORMAL'); 
            $table->text('hint')->nullable();
            $table->timestampsTz();

            $table->unique(['tipo_tramite_id', 'key']);
        });

        DB::statement("ALTER TABLE recaudos_catalogo
            ADD CONSTRAINT chk_tipo_requisito
            CHECK (tipo_requisito IN ('OBLIGATORIO','CONDICIONAL','DIFERIDO'))");

        DB::statement("ALTER TABLE recaudos_catalogo
            ADD CONSTRAINT chk_tipo_documento
            CHECK (tipo_documento IN ('FORMAL', 'FOTO'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('recaudos_catalogo');
    }
};