<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bitacora_eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('modulo', 50)->default('general');
            $table->string('tipo_evento', 50);
            $table->string('descripcion');
            
            $table->string('auditable_type')->nullable();
            $table->string('auditable_id')->nullable(); 

            $table->jsonb('valores_anteriores')->nullable();
            $table->jsonb('valores_nuevos')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestampsTz();

            $table->index(['modulo', 'tipo_evento']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('created_at');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE bitacora_eventos 
                ADD COLUMN nivel nivel_bitacora_enum NOT NULL DEFAULT 'info';
            ");
        } else {
            Schema::table('bitacora_eventos', function (Blueprint $table) {
                $table->enum('nivel', ['info', 'warning', 'danger', 'critical'])->default('info');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bitacora_eventos');
    }
};