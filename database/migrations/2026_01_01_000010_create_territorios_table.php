<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('territorios', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nombre de la Comuna
            $table->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('territorios');
    }
};