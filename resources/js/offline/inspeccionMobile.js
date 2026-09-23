document.addEventListener('alpine:init', () => {
    Alpine.data('inspeccionCampo', (config) => ({
        // Datos del expediente y usuario
        expedienteId: config.expedienteId,
        rolUsuario: config.rolUsuario, // 'director' o 'inspector'
        isOnline: navigator.onLine,
        
        // Estado del formulario
        tabActual: 'evidencias', // 'evidencias', 'checklist', 'dictamen'
        guardando: false,
        
        form: {
            observaciones: '',
            tipo_hallazgo: 'favorable', // favorable, leve, grave
            dictamen_final: '', // Aprobado / Rechazado / Requiere Adecuacion
            requiere_reinspeccion: false,
        },
        
        evidenciasLocales: [],
        firmaCanvas: null,
        firmaContext: null,
        isDrawing: false,

        async init() {
            // Escuchar cambios de conexión
            window.addEventListener('online', () => { this.isOnline = true; this.sincronizar(); });
            window.addEventListener('offline', () => { this.isOnline = false; });

            // Inicializar canvas de firma
            this.$nextTick(() => this.initCanvas());

            // Cargar borrador previo si existe en IndexedDB
            await this.cargarBorradorLocal();
        },

        initCanvas() {
            const canvas = this.$refs.canvasFirma;
            if (!canvas) return;
            
            this.firmaContext = canvas.getContext('2d');
            canvas.width = canvas.parentElement.clientWidth;
            canvas.height = 150;
            this.firmaContext.lineWidth = 2;
            this.firmaContext.strokeStyle = '#0f172a';
        },

        startDrawing(e) {
            this.isDrawing = true;
            const pos = this.getPos(e);
            this.firmaContext.beginPath();
            this.firmaContext.moveTo(pos.x, pos.y);
        },

        draw(e) {
            if (!this.isDrawing) return;
            const pos = this.getPos(e);
            this.firmaContext.lineTo(pos.x, pos.y);
            this.firmaContext.stroke();
        },

        stopDrawing() {
            this.isDrawing = false;
        },

        limpiarFirma() {
            const canvas = this.$refs.canvasFirma;
            this.firmaContext.clearRect(0, 0, canvas.width, canvas.height);
        },

        getPos(e) {
            const rect = this.$refs.canvasFirma.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        },

        async capturarImagen(event) {
            const files = Array.from(event.target.files);
            for (const file of files) {
                // Compresión en cliente para no saturar memoria ni red
                const comprimida = await window.comprimirEvidencia(file);
                
                this.evidenciasLocales.push({
                    id: Date.now() + Math.random(),
                    nombre: file.name,
                    blob: comprimida,
                    previewUrl: URL.createObjectURL(comprimida)
                });
            }
            await this.guardarLocal();
        },

        eliminarEvidencia(id) {
            this.evidenciasLocales = this.evidenciasLocales.filter(e => e.id !== id);
            this.guardarLocal();
        },

        async guardarLocal() {
            this.guardando = true;
            const firmaBlob = await new Promise(res => this.$refs.canvasFirma?.toBlob(res));

            await window.SyncManager.guardarBorradorLocal(this.expedienteId, {
                form: this.form,
                firma: firmaBlob,
                inspector_rol: this.rolUsuario
            }, this.evidenciasLocales);

            this.guardando = false;
        },

        async sincronizar() {
            if (!this.isOnline) return;
            await window.SyncManager.sincronizarPendientes();
        }
    }));
});