# 🌿 Sistema de Gestión Ambiental y Manejo Integral de Residuos (EcoSistema)

Sistema integral desarrollado en **Laravel** para la digitalización, fiscalización, recepción de trámites y monitoreo de declaraciones ambientales y manejo de residuos en la gestión municipal de la Alcaldía del Municipio Carrizal.

---

## 📌 Tabla de Contenidos

1. [Características Principales](#-características-principales)
2. [Arquitectura de Roles](#-arquitectura-de-roles)
3. [Módulos del Sistema](#-módulos-del-sistema)
4. [Reportes Ejecutivos e Informes (Módulo Alcaldesa)](#-reportes-ejecutivos-e-informes-módulo-alcaldesa)
5. [Estructura de Rutas Clave](#-estructura-de-rutas-clave)
6. [Requisitos del Sistema](#-requisitos-del-sistema)
7. [Instalación y Configuración](#-instalación-y-configuración)

---

## 🚀 Características Principales

* **Gestión de Empresas:** Registro de Perfil Ambiental de empresas del municipio, clasificación por tipo de actividad y evaluación de riesgos comerciales.
* **Declaraciones de Residuos Estandarizadas:**
  * Notificación simplificada para bajo impacto (`NO_APLICA`).
  * Registro detallado para empresas **Generadoras** y **Manejadoras/Empresas de Servicios**.
  * Trazabilidad completa (Hojas de seguimiento, RIF de origen/destino, Placas de vehículos, Manifiestos en PDF/imágenes).
  * Conversión automática de cantidades a Toneladas métricas según la unidad de medida utilizada.
* **Fiscalización e Inspecciones:** Asignación de inspectores, programación de auditorías en campo y registro de evidencias en tiempo real.
* **Módulo de Sanciones y Citaciones:** Registro y resolución de procedimientos administrativos con un enfoque **estrictamente presencial** en la Dirección de Residuos para la validación y levantamiento de expedientes.
* **Compensación Ambiental:** Gestión y evaluación de entregas/compromisos ambientales.
* **Panel de Denuncias y Reportes:** Recepción y atención de denuncias ciudadanas geolocalizadas por comuna y sector.

---

## 👥 Arquitectura de Roles

El sistema utiliza un Middleware de control de acceso basado en roles (`role:nombre_rol`):

| Rol | Descripción | Prefijo de Ruta |
| :--- | :--- | :--- |
| **Ciudadano / Empresa** | Declara residuos, solicita trámites, adjunta documentos y consulta su expediente comercial y sancionatorio. | `/ciudadano/*` |
| **Director de Ambiente** | Gestiona trámites ambientales, inspectores, denuncias y compensación ecológica. | `/director/ambiente/*` |
| **Director de Residuos** | Supervisa las declaraciones de residuos, fiscaliza empresas y emite/resuelve sanciones presenciales. | `/director/residuos/*` |
| **Inspector** | Realiza visitas técnicas, evalúa trámites en campo y genera actas de inspección oficiales. | `/inspector/*` |
| **Alcaldesa / Admin** | Visualización de scorecards ejecutivos, reportes consolidados y configuración global. | `/alcaldesa/*` & `/admin/*` |

---

## 🗂️ Módulos del Sistema

### 1. Módulo Ciudadano / Empresa
* **Ficha de Empresa:** Mantiene al día el Perfil Ambiental de la organización y la vigencia de sus documentos de expediente.
* **Declaración de Residuos:** Interfaz modularizada con soporte para carga múltiple de manifiestos y cálculo dinámico por renglón.
* **Control de Sanciones:** Visualización transparente de estatus legales y citaciones pendientes de atención presencial ante la dirección.

### 2. Módulo Director de Residuos
* **Controlador General de Declaraciones (`Director\Residuos\DeclaracionResiduoController`):**
  * Vista unificada de declaraciones ambientales (`/director/residuos/declaraciones`).
  * Filtros dinámicos por Rol Declarante, Estatus y Período.
* **Gestión de Empresas y Sanciones (`Director\Residuos\EmpresaController`):**
  * Historial de fiscalización por empresa.
  * Emisión de actas de citación y resolución de procedimientos una vez cumplido el protocolo presencial.

---

## 📊 Reportes Ejecutivos e Informes (Módulo Alcaldesa)

* **Generador PDF Nativo sin Screenshots:** Diseñado mediante plantillas HTML/CSS optimizadas (`window.print` con iframe oculto) para garantizar una calidad institucional impecable.
* **Elementos Visuales Integrados:**
  * Gráficos circulares de estatus (Donut Charts vectoriales SVG) para mostrar totales y porcentajes de registros resueltos vs. sin resolver.
  * Barras proporcionales dinámicas para clasificar los niveles de gravedad (*Alta, Media, Baja*) y los motivos de denuncia más recurrentes en el municipio.
  * Consolidado territorial estructurado por Comunas y Sectores para evitar saturación de páginas.

---

## 🗺️ Estructura de Rutas Clave

```bash
/
├── /login & /register              # Autenticación con Throttle y branding institucional
├── /dashboard                      # Despachador según Rol
│
├── /ciudadano
│   ├── /empresas                   # Gestión de Empresas y Sanciones
│   ├── /declaraciones              # Historial y Formulario de Declaraciones
│   ├── /solicitudes                # Trámites Ambientales
│   └── /reportes                   # Denuncias Ciudadanas
│
├── /director
│   ├── /ambiente
│   │   ├── /solicitudes            # Asignación y revisión de solicitudes
│   │   ├── /inspectores            # Control de inspectores activos
│   │   └── /reportes               # Atención de denuncias
│   │
│   └── /residuos
│       ├── /declaraciones          # Módulo autónomo de auditoría de declaraciones
│       └── /empresas               # Control, inspecciones y sanciones a empresas
│
└── /alcaldesa
    ├── /reportes                   # Indicadores generales de gestión
    ├── /poder-popular              # Análisis territorial de incidencias
    └── /poder-popular/pdf          # Exportación de informe ejecutivo en PDF