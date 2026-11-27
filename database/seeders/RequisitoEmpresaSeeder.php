<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitoEmpresaCatalogo;

class RequisitoEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $requisitos = [
            // ============ COMUNES A TODOS ============
            [
                'codigo' => 'DOC_NO_OBJECION',
                'nombre' => 'Constancia de No Objeción Ambiental',
                'descripcion' => 'Emitida por la autoridad municipal competente. Requisito fundamental para operar.',
                'aplica_a_rol' => 'TODOS',
                'tipo_requisito' => 'OBLIGATORIO',
                'condicion_flag' => null,
                'es_vitalicio_default' => false,
                'orden' => 1,
                'activo' => true,
            ],
            [
                'codigo' => 'DOC_RENMA',
                'nombre' => 'Registro RENMA',
                'descripcion' => 'Registro Nacional de Manejadores de Sustancias, Materiales y Desechos Peligrosos.',
                'aplica_a_rol' => 'TODOS',
                'tipo_requisito' => 'CONDICIONAL',
                'condicion_flag' => 'genera_peligrosos',
                'es_vitalicio_default' => false,
                'orden' => 2,
                'activo' => true,
            ],

            // ============ GENERADOR + MANEJADOR ============
            [
                'codigo' => 'DOC_RACDA',
                'nombre' => 'Registro RACDA',
                'descripcion' => 'Registro de Actividades Capaces de Degradar el Ambiente.',
                'aplica_a_rol' => 'GENERADOR,MANEJADOR',
                'tipo_requisito' => 'OBLIGATORIO',
                'condicion_flag' => null,
                'es_vitalicio_default' => false,
                'orden' => 3,
                'activo' => true,
            ],
            [
                'codigo' => 'DOC_PLAN_DESECHOS',
                'nombre' => 'Plan de Manejo de Desechos y Residuos Sólidos',
                'descripcion' => 'Protocolo interno de gestión y disposición de residuos sólidos.',
                'aplica_a_rol' => 'GENERADOR,MANEJADOR',
                'tipo_requisito' => 'OBLIGATORIO',
                'condicion_flag' => null,
                'es_vitalicio_default' => false,
                'orden' => 4,
                'activo' => true,
            ],

            // ============ PÓLIZA (2 variantes por rol) ============
            [
                'codigo' => 'DOC_POLIZA',
                'nombre' => 'Póliza de Seguro de Responsabilidad Ambiental',
                'descripcion' => 'Obligatoria para Manejadores. Condicional para Generadores con residuos peligrosos.',
                'aplica_a_rol' => 'MANEJADOR',
                'tipo_requisito' => 'OBLIGATORIO',
                'condicion_flag' => null,
                'es_vitalicio_default' => false,
                'orden' => 5,
                'activo' => true,
            ],
            [
                'codigo' => 'DOC_POLIZA',
                'nombre' => 'Póliza de Seguro de Responsabilidad Ambiental',
                'descripcion' => 'Obligatoria para Manejadores. Condicional para Generadores con residuos peligrosos.',
                'aplica_a_rol' => 'GENERADOR',
                'tipo_requisito' => 'CONDICIONAL',
                'condicion_flag' => 'genera_peligrosos',
                'es_vitalicio_default' => false,
                'orden' => 5,
                'activo' => true,
            ],

            // ============ PLAN EMERGENCIA (2 variantes por rol) ============
            [
                'codigo' => 'DOC_PLAN_EMERGENCIA',
                'nombre' => 'Plan de Emergencia y Contingencia Ambiental',
                'descripcion' => 'Obligatorio para Manejadores. Condicional para Generadores con riesgo ambiental.',
                'aplica_a_rol' => 'MANEJADOR',
                'tipo_requisito' => 'OBLIGATORIO',
                'condicion_flag' => null,
                'es_vitalicio_default' => false,
                'orden' => 6,
                'activo' => true,
            ],
            [
                'codigo' => 'DOC_PLAN_EMERGENCIA',
                'nombre' => 'Plan de Emergencia y Contingencia Ambiental',
                'descripcion' => 'Obligatorio para Manejadores. Condicional para Generadores con riesgo ambiental.',
                'aplica_a_rol' => 'GENERADOR',
                'tipo_requisito' => 'CONDICIONAL',
                'condicion_flag' => 'genera_riesgo_ambiental',
                'es_vitalicio_default' => false,
                'orden' => 6,
                'activo' => true,
            ],

            // ============ CARACTERIZACIONES (solo si genera) ============
            [
                'codigo' => 'DOC_EFLUENTES',
                'nombre' => 'Caracterización de Efluentes Líquidos',
                'descripcion' => 'Análisis de laboratorio o memoria descriptiva de efluentes.',
                'aplica_a_rol' => 'TODOS',
                'tipo_requisito' => 'CONDICIONAL',
                'condicion_flag' => 'genera_efluentes',
                'es_vitalicio_default' => false,
                'orden' => 7,
                'activo' => true,
            ],
            [
                'codigo' => 'DOC_EMISIONES',
                'nombre' => 'Caracterización de Emisiones Atmosféricas',
                'descripcion' => 'Estudio o control de emisiones de gases/partículas.',
                'aplica_a_rol' => 'TODOS',
                'tipo_requisito' => 'CONDICIONAL',
                'condicion_flag' => 'genera_emisiones',
                'es_vitalicio_default' => false,
                'orden' => 8,
                'activo' => true,
            ],
        ];

        foreach ($requisitos as $req) {
            RequisitoEmpresaCatalogo::updateOrCreate(
                [
                    'codigo' => $req['codigo'],
                    'aplica_a_rol' => $req['aplica_a_rol'],
                ],
                $req
            );
        }
    }
}