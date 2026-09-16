// resources/js/offline/syncManager.js
import { db } from './db.js';

export const SyncManager = {
    // Escuchar el estado de conexión a Internet
    initListener(onStatusChange) {
        window.addEventListener('online', () => onStatusChange(true));
        window.addEventListener('offline', () => onStatusChange(false));
    },

    // Guardar borrador de la inspección localmente
    async guardarBorradorLocal(expedienteId, datosFormulario, archivos = []) {
        const inspeccionId = `local_${expedienteId}_${Date.now()}`;

        // 1. Guardar la inspección
        await db.inspecciones.put({
            id: inspeccionId,
            expediente_id: expedienteId,
            datos: datosFormulario,
            estado_sync: 'pendiente',
            updated_at: new Date().toISOString()
        });

        // 2. Guardar archivos comprimidos en IndexedDB
        for (const archivo of archivos) {
            await db.evidencias.add({
                inspeccion_id: inspeccionId,
                blob: archivo.blob,
                nombre: archivo.nombre,
                sync_status: 'pendiente'
            });
        }

        return inspeccionId;
    },

    // Enviar pendientes a Laravel al estar Online
    async sincronizarPendientes() {
        if (!navigator.onLine) return { status: 'offline' };

        const pendientes = await db.inspecciones.where('estado_sync').equals('pendiente').toArray();

        for (const inspeccion of pendientes) {
            const evidencias = await db.evidencias.where('inspeccion_id').equals(inspeccion.id).toArray();
            
            const formData = new FormData();
            formData.append('expediente_id', inspeccion.expediente_id);
            formData.append('datos', JSON.stringify(inspeccion.datos));

            evidencias.forEach((ev, index) => {
                formData.append(`evidencias[${index}]`, ev.blob, ev.nombre);
            });

            try {
                const response = await fetch('/api/ambiente/inspecciones/sincronizar', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                if (response.ok) {
                    // Actualizar estados locales tras sincronización exitosa
                    await db.inspecciones.update(inspeccion.id, { estado_sync: 'sincronizado' });
                    await db.evidencias.where('inspeccion_id').equals(inspeccion.id).modify({ sync_status: 'sincronizado' });
                }
            } catch (err) {
                console.error('Error al sincronizar paquete de inspección:', err);
            }
        }
    }
};