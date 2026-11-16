<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles', 'id_roles')->cascadeOnDelete();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones', 'id_direccion')->nullOnDelete();

            $table->string('cedula', 20)->unique();
            $table->string('telefono', 15);
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('correo')->unique();

            $table->boolean('accepted_terms')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestampTz('email_verified_at')->nullable();

            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('correo');
            $table->index('cedula');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};