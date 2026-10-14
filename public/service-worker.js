// Service Worker de la PWA Cuisinotes.
// Réseau d'abord : l'appli est toujours à jour en ligne ; le cache ne sert que hors connexion.
// Les assets Encore ont des noms hashés en prod, on ne les précharge donc pas : ils sont mis
// en cache au fil de la navigation. Changer CACHE_NAME purge les anciens caches.
const CACHE_NAME = 'cuisinotes-v2';
const PRECACHE = [
  '/favicon.svg',
  '/app-icons/icon-192.png',
  '/app-icons/icon-512.png'
];

self.addEventListener('install', event => {
  event.waitUntil(caches.open(CACHE_NAME).then(cache => cache.addAll(PRECACHE)));
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(names => Promise.all(names.filter(name => name !== CACHE_NAME).map(name => caches.delete(name))))
      .then(() => self.clients.claim())
  );
});

self.addEventListener('fetch', event => {
  const request = event.request;
  // On ne touche qu'aux GET de notre domaine (pas aux formulaires, à l'AJAX POST ni aux CDN)
  if (request.method !== 'GET' || new URL(request.url).origin !== self.location.origin) {
    return;
  }

  event.respondWith(
    fetch(request)
      .then(response => {
        if (response.ok && new URL(request.url).pathname.startsWith('/build/')) {
          const copy = response.clone();
          caches.open(CACHE_NAME).then(cache => cache.put(request, copy));
        }
        return response;
      })
      .catch(() => caches.match(request))
  );
});
