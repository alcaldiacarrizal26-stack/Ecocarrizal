<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tipos ENUM nativos PostgreSQL
        $enums = [
            "tipo_racda_enum"          => "'NO_APLICA', 'RACDA_GENERADOR', 'RACDA_MANEJADOR'",
            "estado_documento_enum"    => "'PENDIENTE', 'APROBADO', 'OBSERVADO'",
            "dictamen_inspeccion_enum" => "'APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'",
            "estatus_compensacion_enum"=> "'PENDIENTE', 'PARCIAL', 'COMPLETADO'",
            "estatus_entrega_enum"     => "'PENDIENTE', 'APROBADO', 'RECHAZADO'",
            "tipo_tramite_enum"        => "'inspeccion', 'poda_deshierbe', 'desmalezamiento_conformacion', 'tala'",
            "estado_solicitud_enum"    => "'pendiente', 'incompleta', 'en_revision', 'inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion', 'aprobado', 'rechazado'",
            "nivel_bitacora_enum"     => "'info', 'warning', 'danger', 'critical'",
        ];

        if (DB::getDriverName() === 'pgsql') {
            foreach ($enums as $name => $values) {
                DB::statement("DROP TYPE IF EXISTS {$name} CASCADE;");
                DB::statement("CREATE TYPE {$name} AS ENUM ({$values});");
            }
        }

        // 2. Tablas Catálogo
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_roles');
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('direcciones', function (Blueprint $table) {
            $table->id('id_direccion');
            $table->string('nombre_direc')->unique();
            $table->text('descripcion_direc')->nullable();
            $table->timestamps();
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->integer('attempt_count')->default(0);
            $table->timestampTz('last_failed_at')->nullable();
            $table->timestampTz('locked_until')->nullable();
        });

        // 3. Usuarios e Inspectores
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

            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('correo');
            $table->index('cedula');
        });

        Schema::create('inspectores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('direccion_id')->constrained('direcciones', 'id_direccion');

            $table->string('codigo_credencial', 50)->unique();
            $table->string('especialidad', 100)->nullable();
            $table->boolean('disponible')->default(true);

            $table->timestamps();
        });

        // 4. Denuncias Ambientales
        Schema::create('denuncias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo', 20)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('inspector_id')->nullable()->constrained('inspectores')->nullOnDelete();

            $table->string('tipo_contaminacion');
            $table->string('otro_tipo_contaminacion')->nullable();
            
            $table->string('comuna');
            $table->string('sector');
            $table->string('punto_referencia');
            
            $table->enum('nivel_gravedad', ['baja', 'media', 'alta'])->default('baja');
            $table->text('justificacion_gravedad')->nullable();
            
            $table->text('descripcion');
            $table->jsonb('evidencias')->nullable();
            
            // Se incluye 'requiere_reinspeccion'
            $table->enum('estatus', [
                'pendiente', 
                'inspector_asignado', 
                'en_inspeccion', 
                'en_revision',
                'requiere_reinspeccion',
                'atendido', 
                'desestimado'
            ])->default('pendiente');
            
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            $table->timestampTz('fecha_inicio_inspeccion')->nullable();
            $table->timestampTz('fecha_cierre')->nullable();

            $table->timestampsTz();

            $table->index('estatus');
            $table->index('created_at');
        });

        // 5. Trámites y Solicitudes
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_expediente')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('inspector_id')->nullable()->constrained('inspectores')->onDelete('set null');

            $table->string('comuna');
            $table->string('sector');
            $table->text('direccion_exacta');
            $table->text('motivo_descripcion');
            $table->jsonb('datos_especificos')->nullable();
            
            // Trazabilidad de fechas
            $table->timestampTz('fecha_limite_documentos')->nullable();
            $table->timestampTz('fecha_asignacion_inspector')->nullable();
            $table->timestampTz('fecha_inicio_inspeccion')->nullable();
            $table->timestampTz('fecha_cierre')->nullable();
            
            $table->timestampsTz();

            $table->index(['user_id', 'created_at']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE solicitudes 
                ADD COLUMN tipo_tramite tipo_tramite_enum NOT NULL,
                ADD COLUMN estado estado_solicitud_enum NOT NULL DEFAULT 'pendiente';
            ");
        } else {
            Schema::table('solicitudes', function (Blueprint $table) {
                $table->enum('tipo_tramite', ['inspeccion', 'poda_deshierbe', 'desmalezamiento_conformacion', 'tala']);
                $table->enum('estado', ['pendiente', 'incompleta', 'en_revision', 'inspector_asignado', 'en_inspeccion', 'requiere_reinspeccion', 'aprobado', 'rechazado'])->default('pendiente');
            });
        }

        Schema::table('solicitudes', function (Blueprint $table) {
            $table->index('estado');
        });

        Schema::create('solicitud_recaudos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('solicitud_id')->constrained('solicitudes')->onDelete('cascade');
            $table->string('tipo_recaudo');
            $table->string('nombre_archivo');
            $table->string('bucket_path');
            $table->string('archivo_url');
            $table->boolean('es_evidencia_fotografica')->default(false);
            
            $table->timestampsTz();

            $table->index('solicitud_id');
        });

        // 6. Empresas y Expedientes
        Schema::create('empresas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('rif', 15)->unique();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('numero_cuenta_patente');
            $table->string('correo_comercial');
            $table->string('telefono_comercial');
            
            $table->string('comuna');
            $table->string('sector');
            $table->string('calle_avenida');
            $table->string('edificio_galpon_local');
            $table->text('punto_referencia')->nullable();

            $table->enum('estatus', [
                'PENDIENTE_APROBACION', 
                'OBSERVADO', 
                'APROBADO', 
                'RECHAZADO'
            ])->default('PENDIENTE_APROBACION');

            $table->text('observaciones_fiscalia')->nullable();
            $table->timestamps();

            $table->index('estatus');
        });

        Schema::create('perfiles_ambientales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();

            $table->string('rol_ambiental')->default('GENERADOR');
            $table->string('subtipo_generador')->nullable();
            $table->string('numero_racda')->nullable();
            
            $table->boolean('genera_efluentes')->default(false);
            $table->boolean('genera_emisiones')->default(false);
            $table->boolean('genera_peligrosos')->default(false);
            $table->boolean('genera_ruidos')->default(false);
            $table->boolean('usa_pozo_agua')->default(false);

            $table->string('manejadora_contratada')->nullable();
            $table->jsonb('actividades_manejador')->nullable();
            $table->text('sustancias_manejadas')->nullable();
            $table->jsonb('residuos_tipos')->nullable();

            $table->jsonb('residuos_comunes')->nullable();
            $table->jsonb('residuos_reciclables')->nullable();
            $table->jsonb('residuos_peligrosos')->nullable();

            $table->timestamps();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE perfiles_ambientales 
                ADD COLUMN tipo_racda tipo_racda_enum NOT NULL DEFAULT 'NO_APLICA';
            ");
        } else {
            Schema::table('perfiles_ambientales', function (Blueprint $table) {
                $table->enum('tipo_racda', ['NO_APLICA', 'RACDA_GENERADOR', 'RACDA_MANEJADOR'])->default('NO_APLICA');
            });
        }

        Schema::create('documentos_expediente', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('empresa_id')->constrained('empresas')->cascadeOnDelete();

            $table->string('codigo_requisito');
            $table->text('nombre_requisito');
            $table->string('numero_folio')->nullable();
            $table->text('archivo_path')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE documentos_expediente 
                ADD COLUMN estado estado_documento_enum NOT NULL DEFAULT 'PENDIENTE';
            ");
        } else {
            Schema::table('documentos_expediente', function (Blueprint $table) {
                $table->enum('estado', ['PENDIENTE', 'APROBADO', 'OBSERVADO'])->default('PENDIENTE');
            });
        }

        // 7. Inspecciones y Acta de Evaluación
        Schema::create('inspecciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_acta', 30)->unique();
            
            $table->foreignUuid('solicitud_id')->nullable()->constrained('solicitudes')->nullOnDelete();
            $table->foreignUuid('denuncia_id')->nullable()->constrained('denuncias')->nullOnDelete();
            $table->foreignId('inspector_id')->constrained('inspectores')->cascadeOnDelete();

            $table->timestampTz('fecha_visita');
            $table->text('observaciones_campo');
            $table->jsonb('hallazgos')->nullable();
            $table->jsonb('fotos_evidencia')->nullable();
            $table->text('recomendaciones')->nullable();
            
            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE inspecciones 
                ADD COLUMN dictamen dictamen_inspeccion_enum NOT NULL DEFAULT 'CON_OBSERVACIONES';
            ");
        } else {
            Schema::table('inspecciones', function (Blueprint $table) {
                $table->enum('dictamen', ['APROBADO', 'CON_OBSERVACIONES', 'RECHAZADO', 'REINSPECCION_REQUERIDA'])->default('CON_OBSERVACIONES');
            });
        }

        // 8. Módulo de Compensación Ambiental
        Schema::create('compensaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('codigo_resolucion', 30)->unique();
            
            $table->foreignUuid('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('inspeccion_id')->nullable()->constrained('inspecciones')->nullOnDelete();

            $table->string('especie_requerida');
            $table->integer('cantidad_asignada')->default(0);
            $table->integer('cantidad_entregada')->default(0);

            $table->timestampTz('fecha_limite')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE compensaciones 
                ADD COLUMN estatus estatus_compensacion_enum NOT NULL DEFAULT 'PENDIENTE';
            ");
        } else {
            Schema::table('compensaciones', function (Blueprint $table) {
                $table->enum('estatus', ['PENDIENTE', 'PARCIAL', 'COMPLETADO'])->default('PENDIENTE');
            });
        }

        Schema::create('entregas_compensacion', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('compensacion_id')->constrained('compensaciones')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->integer('cantidad_entregada');
            $table->string('especie_entregada');
            $table->text('comprobante_path');
            $table->text('observacion_ciudadano')->nullable();
            $table->text('observacion_director')->nullable();
            $table->timestampTz('fecha_revision')->nullable();

            $table->timestampsTz();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("
                ALTER TABLE entregas_compensacion 
                ADD COLUMN estatus estatus_entrega_enum NOT NULL DEFAULT 'PENDIENTE';
            ");
        } else {
            Schema::table('entregas_compensacion', function (Blueprint $table) {
                $table->enum('estatus', ['PENDIENTE', 'APROBADO', 'RECHAZADO'])->default('PENDIENTE');
            });
        }

        // 9. Tabla de Bitácora / Auditoría de Eventos e Intentos Fallidos
        Schema::create('bitacora_eventos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            // Usuario en sesión (null para usuarios no autenticados o fallos de login)
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            $table->string('modulo', 50)->default('general'); // 'solicitudes', 'denuncias', 'autenticacion', 'compensaciones'
            $table->string('tipo_evento', 50); // 'crear', 'editar', 'eliminar', 'intento_fallido', 'acceso_denegado'
            
            $table->string('descripcion');
            
            // Permite vincular la bitácora tanto a modelos con UUID (solicitud, denuncia) como con BIGINT (user)
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
        Schema::dropIfExists('entregas_compensacion');
        Schema::dropIfExists('compensaciones');
        Schema::dropIfExists('inspecciones');
        Schema::dropIfExists('documentos_expediente');
        Schema::dropIfExists('perfiles_ambientales');
        Schema::dropIfExists('empresas');
        Schema::dropIfExists('solicitud_recaudos');
        Schema::dropIfExists('solicitudes');
        Schema::dropIfExists('denuncias');
        Schema::dropIfExists('inspectores');
        Schema::dropIfExists('users');
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('direcciones');
        Schema::dropIfExists('roles');

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("DROP TYPE IF EXISTS nivel_bitacora_enum CASCADE");
            DB::statement("DROP TYPE IF EXISTS estado_solicitud_enum CASCADE");
            DB::statement("DROP TYPE IF EXISTS tipo_tramite_enum CASCADE");
            DB::statement("DROP TYPE IF EXISTS estatus_entrega_enum CASCADE");
            DB::statement("DROP TYPE IF EXISTS estatus_compensacion_enum CASCADE");
            DB::statement("DROP TYPE IF EXISTS estado_documento_enum CASCADE");
            DB::statement("DROP TYPE IF EXISTS tipo_racda_enum CASCADE");
        }
    }
};