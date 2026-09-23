import imageCompression from 'browser-image-compression';

export async function comprimirEvidencia(file) {
    const opciones = {
        maxSizeMB: 0.4,           // Tamaño máximo ~400 KB
        maxWidthOrHeight: 1280,   // Resolución adecuada para evidencias visuales
        useWebWorker: true,
        fileType: 'image/jpeg'
    };

    try {
        const archivoComprimido = await imageCompression(file, opciones);
        return archivoComprimido;
    } catch (error) {
        console.error('Error al comprimir la imagen localmente:', error);
        throw error;
    }
}