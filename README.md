# 🌿 Sistema de Gestión Ambiental y Manejo Integral de Residuos (EcoSistema)

Sistema integral desarrollado en **Laravel** para la digitalización, fiscalización, recepción de trámites y monitoreo de declaraciones ambientales y manejo de residuos en la gestión municipal.

---

## 📌 Tabla de Contenidos

1. [Características Principales](#-características-principales)
2. [Arquitectura de Roles](#-arquitectura-de-roles)
3. [Módulos del Sistema](#-módulos-del-sistema)
4. [Estructura de Rutas Clave](#-estructura-de-rutas-clave)
5. [Requisitos del Sistema](#-requisitos-del-sistema)
6. [Instalación y Configuración](#-instalación-y-configuración)
7. [Buenas Prácticas y Convenciones](#-buenas-prácticas-y-convenciones)

---

## 🚀 Características Principales

* **Gestión de Empresas:** Registro de Perfil Ambiental de empresas del municipio, clasificación por tipo de actividad y evaluación de riesgos.
* **Declaraciones de Residuos Estandarizadas:**
  * Notificación simplificada para bajo impacto (`NO_APLICA`).
  * Registro detallado para empresas **Generadoras** y **Manejadoras/Empresas de Servicios**.
  * Trazabilidad completa (Hojas de seguimiento, RIF de origen/destino, Placas de vehículos, Manifiestos en PDF/imágenes).
  * Conversión automática de cantidades a Toneladas métricas según la unidad de medida utilizada.
* **Fiscalización e Inspecciones:** Asignación de inspectores, programación de auditorías en campo y registro de evidencias en tiempo real.
* **Módulo de Sanciones y Citaciones:** Registro y resolución de procedimientos administrativos.
* **Compensación Ambiental:** Gestión y evaluación de entregas/compromisos ambientales.
* **Panel de Denuncias y Reportes:** Recepción y atención de denuncias ciudadanas.

---

## 👥 Arquitectura de Roles

El sistema utiliza un Middleware de control de acceso basado en roles (`role:nombre_rol`):

| Rol | Descripción | Prefijo de Ruta |
| :--- | :--- | :--- |
| **Ciudadano / Empresa** | Declara residuos, solicita trámites, adjunta documentos y gestiona su perfil. | `/ciudadano/*` |
| **Director de Ambiente** | Gestiona trámites ambientales, inspectores, denuncias y compensación. | `/director/ambiente/*` |
| **Director de Residuos** | Supervisa las declaraciones de residuos, fiscaliza empresas y emite sanciones. | `/director/residuos/*` |
| **Inspector** | Realiza visitas técnicas, evalúa trámites en campo y genera actas de inspección. | `/inspector/*` |
| **Administrador** | Configuración global del sistema y gestión de usuarios. | `/admin/*` |

---

## 🗂️ Módulos del Sistema

### 1. Módulo Ciudadano / Empresa
* **Ficha de Empresa:** Mantiene al día el Perfil Ambiental de la organización.
* **Declaración de Residuos:** Interfaz modularizada con soporte para carga múltiple de manifiestos y cálculo dinámico por renglón.

### 2. Módulo Director de Residuos
* **Controlador General de Declaraciones (`Director\Residuos\DeclaracionResiduoController`):**
  * Vista unificada de declaraciones ambientales (`/director/residuos/declaraciones`).
  * Filtros dinámicos por Rol Declarante (`GENERADOR`, `MANEJADOR`, `NO_APLICA`), Estatus y Período.
  * Flujo de revisión y cambio de estatus (*PRESENTADA*, *EN_REVISION*, *APROBADA*, *OBSERVADA*, *RECHAZADA*).
* **Gestión de Empresas y Sanciones (`Director\Residuos\EmpresaController`):**
  * Historial de fiscalización por empresa.
  * Emisión y resolución de citaciones y sanciones.

---

## 🗺️ Estructura de Rutas Clave

```bash
/
├── /login & /register              # Autenticación con Throttle
├── /dashboard                      # Despachador según Rol
│
├── /ciudadano
│   ├── /empresas                   # Gestión de Empresas
│   ├── /declaraciones              # Historial y Formulario de Declaraciones
│   ├── /solicitudes                # Trámites Ambientales
│   └── /reportes                   # Denuncias Ciudadanas
│
└── /director
    ├── /ambiente
    │   ├── /solicitudes            # Asignación y revisión de solicitudes
    │   ├── /inspectores            # Control de inspectores activos
    │   └── /reportes               # Atención de denuncias
    │
    └── /residuos
        ├── /declaraciones          # Módulo autónomo de auditoría de declaraciones
        └── /empresas               # Control, inspecciones y sanciones a empresas