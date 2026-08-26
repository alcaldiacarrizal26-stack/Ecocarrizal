<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // -------------------------------------------------------------
        // 1. Tipos ENUM nativos de PostgreSQL (Protegidos contra duplicados)
        // -------------------------------------------------------------
        $enums = [
            "tipo_tramite_enum" => "'poda', 'tala', 'trasplante', 'conformidad', 'racda'",
            "estado_solicitud_enum" => "'En proceso', 'En inspección', 'Aprobado', 'Rechazado'",
            "tipo_racda_enum" => "'NO_APLICA', 'RACDA_GENERADOR', 'RACDA_MANEJADOR'",
            "estado_documento_enum" => "'PENDIENTE', 'APROBADO', 'OBSERVADO'",
        ];

        foreach ($enums as $name => $values) {
            DB::statement("
                DO $$ BEGIN
                    CREATE TYPE {$name} AS ENUM ({$values});
                EXCEPTION
                    WHEN duplicate_object THEN NULL;
                END $$;
            ");
        }

        // -------------------------------------------------------------
        // 2. Tablas Catálogo / Independientes
        // -------------------------------------------------------------
        Schema::create('roles', function (Blueprint $table) {
            $table->integer('id_roles')->primary();
            $table->text('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->timestampTz('created_at')->nullable();
        });

        Schema::create('direcciones', function (Blueprint $table) {
            $table->integer('id_direccion')->primary();
            $table->text('nombre_direc')->unique();
            $table->text('descripcion_direc')->nullable();
            $table->timestampTz('created_at')->nullable();
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->text('email')->primary();
            $table->integer('attempt_count');
            $table->timestampTz('last_failed_at')->nullable();
            $table->timestampTz('locked_until')->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas con nombres personalizados
            $table->foreignId('role_id')->constrained('roles', 'id_roles')->onDelete('cascade');
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones', 'id_direccion')->nullOnDelete();

            // Campos unificados
            $table->string('cedula', 20)->unique();    // Ej: V-12345678 o J-123456789-0
            $table->string('telefono', 15);            // Ej: 04121234567
            
            // Datos personales
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('correo')->unique();

            // Auth
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // -------------------------------------------------------------
        // 3. Perfiles de Usuario
        // -------------------------------------------------------------
        Schema::create('profiles', function (Blueprint $table) {
            $table->foreignId('id_profiles')->primary()->constrained('users')->cascadeOnDelete();
            
            $table->text('email')->nullable();
            $table->text('nombres');
            $table->text('apellidos');
            $table->text('cedula')->nullable();
            $table->text('telefono')->nullable();
            $table->boolean('accepted_terms')->nullable();
            
            $table->integer('role_id');
            $table->foreign('role_id')->references('id_roles')->on('roles');

            $table->boolean('is_active')->nullable()->default(true);
            
            $table->integer('direction_id')->nullable();
            $table->foreign('direction_id')->references('id_direccion')->on('direcciones')->nullOnDelete();

            $table->timestampsTz();
        });

        // -------------------------------------------------------------
        // 4. Denuncias Ambientales
        // -------------------------------------------------------------
        Schema::create('denuncias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->text('tipo_contaminacion');
            $table->text('sector');
            $table->text('punto_referencia');
            $table->text('descripcion');
            $table->jsonb('evidencias')->nullable();

            $table->timestampTz('created_at')->nullable();
        });

        // -------------------------------------------------------------
        // 5. Trámites y Solicitudes
        // -------------------------------------------------------------
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_expediente')->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('sector');
            $table->text('direccion_exacta');
            $table->text('motivo_descripcion');
            $table->jsonb('datos_especificos')->nullable();

            $table->text('proximo_paso')->nullable();

            $table->timestampsTz();
        });

        DB::statement("ALTER TABLE solicitudes ADD COLUMN tipo_tramite tipo_tramite_enum");
        DB::statement("ALTER TABLE solicitudes ADD COLUMN estado estado_solicitud_enum");

        Schema::create('solicitud_recaudos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('solicitud_id');
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->cascadeOnDelete();

            $table->string('tipo_recaudo');
            $table->text('nombre_archivo');
            $table->text('bucket_path');
            $table->text('archivo_url');
            $table->boolean('es_evidencia_fotografica')->nullable();

            $table->timestampTz('created_at')->nullable();
        });

        // -------------------------------------------------------------
        // 6. Empresas y Expedientes Ambientales
        // -------------------------------------------------------------
        Schema::create('empresas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('rif')->unique();
            $table->text('razon_social');
            $table->text('nombre_comercial')->nullable();
            $table->string('numero_cuenta_patente');
            $table->text('correo_comercial');
            $table->string('telefono_comercial');
            $table->text('sector');
            $table->text('calle_avenida');
            $table->text('edificio_galpon_local');
            $table->text('punto_referencia');

            $table->timestampsTz();
        });

        Schema::create('perfiles_ambientales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();

            $table->boolean('tiene_renma')->nullable();
            $table->string('numero_racda')->nullable();

            $table->boolean('genera_efluentes')->nullable();
            $table->boolean('genera_emisiones')->nullable();
            $table->boolean('genera_ruidos')->nullable();
            $table->boolean('usa_pozo_agua')->nullable();

            $table->jsonb('residuos_comunes');
            $table->jsonb('residuos_reciclables');
            $table->jsonb('residuos_peligrosos');

            $table->timestampsTz();
        });

        DB::statement("ALTER TABLE perfiles_ambientales ADD COLUMN tipo_racda tipo_racda_enum");

        Schema::create('documentos_expediente', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('empresa_id');
            $table->foreign('empresa_id')->references('id')->on('empresas')->cascadeOnDelete();

            $table->string('codigo_requisito');
            $table->text('nombre_requisito');
            $table->string('numero_folio')->nullable();
            $table->text('archivo_path')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestampTz('created_at')->nullable();
        });

        DB::statement("ALTER TABLE documentos_expediente ADD COLUMN estado estado_documento_enum");
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_expediente');
        Schema::dropIfExists('perfiles_ambientales');
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('solicitud_recaudos');
        Schema::dropIfExists('solicitudes');
        Schema::dropIfExists('denuncias');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('users');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('direcciones');
        Schema::dropIfExists('roles');

        DB::statement("DROP TYPE IF EXISTS estado_documento_enum CASCADE");
        DB::statement("DROP TYPE IF EXISTS tipo_racda_enum CASCADE");
        DB::statement("DROP TYPE IF EXISTS estado_solicitud_enum CASCADE");
        DB::statement("DROP TYPE IF EXISTS tipo_tramite_enum CASCADE");
    }
};