<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('titulo');
            $table->text('mensaje');
            $table->string('tipo')->default('info'); // success, warning, danger, info
            $table->string('link_url')->nullable(); // Redirección directa al hacer clic
            
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'leido_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};