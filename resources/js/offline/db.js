import Dexie from 'dexie';

export const db = new Dexie('AmbienteInspeccionesDB');

db.version(1).stores({
    // Expedientes descargados para trabajar offline
    expedientes: 'id, codigo_expediente, tipo_tramite, estado',
    
    // Borradores de inspección guardados localmente
    inspecciones: 'id, expediente_id, estado_sync, updated_at',
    
    // Evidencias (fotos/documentos comprimidos)
    evidencias: '++id, inspeccion_id, blob, nombre, sync_status',
    
    // Cola de sincronización para enviar a Laravel al recuperar conexión
    sync_queue: '++id, inspeccion_id, intento, created_at'
});