const CACHE_NAME = 'jobservice-v1';
const urlsToCache = [
    '/',
    '/home',
    '/manifest.json',
    '/assets/css/style.css',
    '/assets/js/main.js'
];

// تثبيت الـ Service Worker وتخزين الملفات الأساسية
self.addEventListener('install', function(e) {
    console.log('Service Worker Installed');
    e.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            console.log('Caching app shell');
            return cache.addAll(urlsToCache);
        })
    );
});

// تنشيط الـ Service Worker وتنظيف الكاش القديم
self.addEventListener('activate', function(e) {
    console.log('Service Worker Activated');
    e.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// استراتيجية التخزين: الكاش أولاً ثم الشبكة
self.addEventListener('fetch', function(e) {
    e.respondWith(
        caches.match(e.request).then(function(response) {
            return response || fetch(e.request).then(function(fetchResponse) {
                return caches.open(CACHE_NAME).then(function(cache) {
                    cache.put(e.request, fetchResponse.clone());
                    return fetchResponse;
                });
            });
        })
    );
});