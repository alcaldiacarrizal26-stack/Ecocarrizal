<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sectores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('territorio_id')->constrained('territorios')->onDelete('cascade');
            $table->string('name'); // Nombre del Sector
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sectores');
    }
};