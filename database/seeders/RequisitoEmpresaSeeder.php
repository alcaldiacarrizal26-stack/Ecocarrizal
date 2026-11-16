<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitoEmpresaCatalogo;

class RequisitoEmpresaSeeder extends Seeder
{
    public function run(): void
    {
        $requisitos = [
            [
                'codigo' => 'DOC_RENMA',
                'nombre' => 'Registro RENMA',
                'descripcion' => 'Registro Nacional de Manejadores de Sustancias, Materiales y Desechos Peligrosos.',
                'es_obligatorio' => true,
            ],
            [
                'codigo' => 'DOC_NO_OBJECION',
                'nombre' => 'Constancia de No Objeción Ambiental',
                'descripcion' => 'Emitida por la autoridad municipal competente.',
                'es_obligatorio' => true,
            ],
            [
                'codigo' => 'DOC_RACDA',
                'nombre' => 'Registro RACDA',
                'descripcion' => 'Constancia de inscripción o registro actualizado.',
                'es_obligatorio' => false,
            ],
            [
                'codigo' => 'DOC_POLIZA',
                'nombre' => 'Póliza de Seguro de Responsabilidad Ambiental',
                'descripcion' => 'Póliza vigente contra daños ambientales.',
                'es_obligatorio' => true,
            ],
            [
                'codigo' => 'DOC_EFLUENTES',
                'nombre' => 'Caracterización de Efluentes Líquidos',
                'descripcion' => 'Análisis de laboratorio o memoria descriptiva.',
                'es_obligatorio' => false,
            ],
            [
                'codigo' => 'DOC_EMISIONES',
                'nombre' => 'Caracterización de Emisiones Atmosféricas',
                'descripcion' => 'Estudio o control de emisiones de gases/partículas.',
                'es_obligatorio' => false,
            ],
            [
                'codigo' => 'DOC_PLAN_DESECHOS',
                'nombre' => 'Plan de Manejo de Desechos y Residuos Sólidos',
                'descripcion' => 'Protocolo interno de gestión y disposición.',
                'es_obligatorio' => true,
            ],
            [
                'codigo' => 'DOC_PLAN_EMERGENCIA',
                'nombre' => 'Plan de Emergencia y Contingencia Ambiental',
                'descripcion' => 'Acciones preventivas ante derrames o siniestros.',
                'es_obligatorio' => true,
            ],
        ];

        foreach ($requisitos as $req) {
            RequisitoEmpresaCatalogo::updateOrCreate(
                ['codigo' => $req['codigo']],
                [
                    'nombre' => $req['nombre'],
                    'descripcion' => $req['descripcion'],
                    'es_obligatorio' => $req['es_obligatorio'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}