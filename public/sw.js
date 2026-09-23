const CACHE_NAME = 'ambiente-inspeccion-v1';
const ASSETS = [
    '/director/ambiente/inspecciones',
    '/css/app.css',
    '/js/app.js',
    '/offline.html'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(ASSETS))
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request).catch(() => caches.match('/offline.html'));
        })
    );
});