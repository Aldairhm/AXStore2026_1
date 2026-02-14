const CACHE_NAME = 'axstore-v1';
const urlsToCache = [
  './',
  'app/views/assets/css/bootstrap.min.css',
  'app/views/assets/css/all.min.css',
  'app/views/assets/css/bootstrap-icons.min.css',
  'app/views/assets/css/style.css',
  'app/views/assets/css/card.css',
  'app/views/assets/css/enviosHome.css',
  'app/views/assets/js/jquery-3.7.1.min.js',
  'app/views/assets/js/bootstrap.bundle.min.js',
  'app/views/assets/js/script.js',
  'app/views/assets/images/logo.png'
];

// Instalación del Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Archivos en caché');
        return cache.addAll(urlsToCache);
      })
  );
});

// Activación y limpieza de cachés viejas
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Estrategia: Network First (Intenta internet, si falla usa caché)
self.addEventListener('fetch', event => {
  event.respondWith(
    fetch(event.request)
      .catch(() => {
        return caches.match(event.request);
      })
  );
});