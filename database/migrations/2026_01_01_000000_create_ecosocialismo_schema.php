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

        // -------------------------------------------------------------
        // 3. Usuarios (Tabla Unificada de Usuarios)
        // -------------------------------------------------------------
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // Llaves foráneas
            $table->foreignId('role_id')->constrained('roles', 'id_roles')->onDelete('cascade');
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones', 'id_direccion')->nullOnDelete();

            // Identificación y contacto
            $table->string('cedula', 20)->unique();
            $table->string('telefono', 15);
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('correo')->unique();

            // Atributos de estado
            $table->boolean('accepted_terms')->default(true);
            $table->boolean('is_active')->default(true);

            // Autenticación
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // -------------------------------------------------------------
        // 4. Denuncias Ambientales
        // -------------------------------------------------------------
        Schema::create('denuncias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('tipo_contaminacion');
            $table->string('otro_tipo_contaminacion')->nullable();
            
            // Ubicación
            $table->string('comuna');
            $table->string('sector');
            $table->string('punto_referencia');
            
            // Severidad
            $table->enum('nivel_gravedad', ['baja', 'media', 'alta'])->default('baja');
            $table->text('justificacion_gravedad')->nullable();
            
            $table->text('descripcion');
            $table->json('evidencias');
            $table->enum('estatus', [
                'pendiente', 
                'inspector_asignado', 
                'en_revision', 
                'atendido', 
                'desestimado'
            ])->default('pendiente');
            
            $table->timestamps();
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
            
            // Vinculación directa con el ciudadano solicitante
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('rif', 15)->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('numero_cuenta_patente')->comment('Cuenta SIGAT / Licencia (CZ-AE-XXXXXX o Cód. Contribuyente)');
            $table->string('correo_comercial');
            $table->string('telefono_comercial');
            
            // Ubicación Territorial Carrizal
            $table->string('comuna'); // <-- NUEVO: Obligatorio para vincular TerritorioService
            $table->string('sector');
            $table->string('calle_avenida');
            $table->string('edificio_galpon_local');
            $table->text('punto_referencia')->nullable();

            // Estado del expediente controlado por la Dirección de Residuos
            $table->enum('estatus', [
                'PENDIENTE_APROBACION', 
                'OBSERVADO', 
                'APROBADO', 
                'RECHAZADO'
            ])->default('PENDIENTE_APROBACION');

            $table->text('observaciones_fiscalia')->nullable();
            $table->timestamps();
        });

        Schema::create('perfiles_ambientales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();

            // Rol Ecosocialista (GENERADOR, MANEJADOR, NO_APLICA)
            $table->string('rol_ambiental')->default('GENERADOR');
            $table->string('subtipo_generador')->nullable(); // PEQUENO, MEDIANO, GRAN
            $table->string('numero_racda')->nullable();
            
            // Diagnóstico Específico
            $table->boolean('genera_efluentes')->default(false);
            $table->boolean('genera_emisiones')->default(false);
            $table->boolean('genera_peligrosos')->default(false);
            $table->boolean('genera_ruidos')->default(false);
            $table->boolean('usa_pozo_agua')->default(false);

            // Datos Complementarios de Manejo / RACDA
            $table->string('manejadora_contratada')->nullable();
            $table->jsonb('actividades_manejador')->nullable(); // Array de actividades seleccionadas
            $table->text('sustancias_manejadas')->nullable();
            $table->jsonb('residuos_tipos')->nullable();       // Array de tipos de residuos

            // Volúmenes (Estructuras JSONB opcionales)
            $table->jsonb('residuos_comunes')->nullable();
            $table->jsonb('residuos_reciclables')->nullable();
            $table->jsonb('residuos_peligrosos')->nullable();

            $table->timestamps();
        });

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