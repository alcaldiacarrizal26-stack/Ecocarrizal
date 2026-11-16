<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoTramite;
use App\Models\RecaudoCatalogo;

class TipoTramiteYRecaudoSeeder extends Seeder
{
    public function run(): void
    {
        $datos = [
            'inspeccion' => [
                'nombre' => 'Solicitud de Inspección TÉCNICA',
                'descripcion' => 'Evaluación e inspección en sitio por parte del personal técnico ambiental.',
                'plazo_dias' => 0,
                'recaudos' => [
                    'obligatorios' => [
                        ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'PDF o imagen legible']
                    ]
                ]
            ],
            'poda_deshierbe' => [
                'nombre' => 'Solicitud de Permiso para Poda o Deshierbe',
                'descripcion' => 'Autorización técnica para labores de mantenimiento, fitosanitarias o poda de formación de vegetación.',
                'plazo_dias' => 31,
                'recaudos' => [
                    'obligatorios' => [
                        ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                        ['key' => 'carta_explicativa', 'label' => 'Carta Explicativa del Trabajo a Realizar', 'hint' => 'Detallar motivos y ramas a intervenir']
                    ],
                    'condicionales' => [
                        ['key' => 'autorizacion_propietario', 'label' => 'Autorización del Propietario', 'hint' => 'Requerido si no eres el propietario directo'],
                        ['key' => 'variables_ambientales', 'label' => 'Variables Ambientales (MINEC)', 'hint' => 'Requerido si se encuentra en Zona Protectora'],
                        ['key' => 'registro_mercantil', 'label' => 'Copia del Registro Mercantil', 'hint' => 'Requerido para Personas Jurídicas'],
                        ['key' => 'rif_empresa', 'label' => 'Copia del RIF Vigente de la Empresa', 'hint' => 'Requerido para Personas Jurídicas']
                    ],
                    'opcionales' => [
                        ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                        ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                        ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                        ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal']
                    ]
                ]
            ],
            'tala' => [
                'nombre' => 'Solicitud de Permiso para Tala de Árboles',
                'descripcion' => 'Autorización técnica para el derribo/remoción de ejemplares arbóreos por riesgo, infraestructura o enfermedad.',
                'plazo_dias' => 31,
                'recaudos' => [
                    'obligatorios' => [
                        ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                        ['key' => 'carta_explicativa', 'label' => 'Carta Justificativa de Tala', 'hint' => 'Detallar motivos de riesgo o daño'],
                        ['key' => 'carta_compromiso', 'label' => 'Carta de Compromiso de Compensación Ambiental', 'hint' => 'Compromiso firmado para reforestación']
                    ],
                    'condicionales' => [
                        ['key' => 'autorizacion_propietario', 'label' => 'Autorización del Propietario', 'hint' => 'Requerido si no eres el propietario'],
                        ['key' => 'variables_ambientales', 'label' => 'Variables Ambientales (MINEC)', 'hint' => 'Requerido en Zona Protectora'],
                        ['key' => 'registro_mercantil', 'label' => 'Copia del Registro Mercantil', 'hint' => 'Requerido para Personas Jurídicas'],
                        ['key' => 'rif_empresa', 'label' => 'Copia del RIF Vigente de la Empresa', 'hint' => 'Requerido para Personas Jurídicas']
                    ],
                    'opcionales' => [
                        ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                        ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                        ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                        ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal']
                    ]
                ]
            ],
            'desmalezamiento_conformacion' => [
                'nombre' => 'Permiso de Desmalezamiento y Conformación de Terreno',
                'descripcion' => 'Permisos para trabajos de limpieza profunda, acondicionamiento y movimiento menor de suelos.',
                'plazo_dias' => 31,
                'recaudos' => [
                    'obligatorios' => [
                        ['key' => 'cedula', 'label' => 'Copia de la Cédula de Identidad', 'hint' => 'Requisito inicial obligatorio'],
                        ['key' => 'carta_explicativa', 'label' => 'Carta Explicativa del Trabajo a Realizar', 'hint' => 'Detallar intervención de suelo']
                    ],
                    'condicionales' => [
                        ['key' => 'autorizacion_propietario', 'label' => 'Autorización del Propietario', 'hint' => 'Requerido si no eres el propietario'],
                        ['key' => 'variables_ambientales', 'label' => 'Variables Ambientales (MINEC)', 'hint' => 'Requerido en Zona Protectora'],
                        ['key' => 'registro_mercantil', 'label' => 'Copia del Registro Mercantil', 'hint' => 'Requerido para Personas Jurídicas'],
                        ['key' => 'rif_empresa', 'label' => 'Copia del RIF Vigente de la Empresa', 'hint' => 'Requerido para Personas Jurídicas']
                    ],
                    'opcionales' => [
                        ['key' => 'doc_propiedad', 'label' => 'Documento de Propiedad Registrado', 'hint' => 'Título o documento legal'],
                        ['key' => 'plano_ubicacion', 'label' => 'Plano de Ubicación del Terreno', 'hint' => 'Croquis o plano de mensura'],
                        ['key' => 'solvencia_municipal', 'label' => 'Solvencia Municipal Vigente', 'hint' => 'Emitida por la Alcaldía'],
                        ['key' => 'constancia_catastral', 'label' => 'Constancia Catastral Vigente', 'hint' => 'Catastro municipal'],
                        ['key' => 'carta_compromiso', 'label' => 'Carta de Compromiso de Reposición', 'hint' => 'En caso de afectación a bienes públicos']
                    ]
                ]
            ]
        ];

        foreach ($datos as $slug => $info) {
            $tipoTramite = TipoTramite::updateOrCreate(
                ['slug' => $slug],
                [
                    'nombre' => $info['nombre'],
                    'descripcion' => $info['descripcion'],
                    'plazo_dias' => $info['plazo_dias'],
                ]
            );

            foreach ($info['recaudos'] as $tipoRequisito => $requisitos) {
                foreach ($requisitos as $req) {
                    RecaudoCatalogo::updateOrInsert(
                        ['tipo_tramite' => $tipoTramite->slug, 'key' => $req['key']],
                        [
                            'label' => $req['label'],
                            'tipo_requisito' => rtrim($tipoRequisito, 's'),
                            'hint' => $req['hint'] ?? null,
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}