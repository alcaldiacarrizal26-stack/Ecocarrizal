<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credenciales_inspector', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            $table->string('codigo_credencial', 50)->unique();
            $table->string('especialidad', 100)->nullable();
            $table->boolean('disponible')->default(true);

            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credenciales_inspector');
    }
};