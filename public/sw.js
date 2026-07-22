/**
 * Omul Potrivit Service Worker
 * Handles push notifications, offline caching, and PWA functionality
 */

const CACHE_NAME = 'omulpotrivit-v2';
const OFFLINE_URL = '/offline.html';

// Assets to cache for offline use
const STATIC_ASSETS = [
    '/',
    '/manifest.json',
    '/images/logo.png'
];

// Dynamic cache patterns
const CACHE_PATTERNS = {
    images: /\.(png|jpg|jpeg|gif|webp|svg|ico)$/i,
    styles: /\.(css)$/i,
    scripts: /\.(js)$/i,
    fonts: /\.(woff|woff2|ttf|eot)$/i
};

/**
 * Install Event - Cache static assets
 */
self.addEventListener('install', function(event) {
    console.log('[SW] Installing service worker...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('[SW] Caching static assets');
                return cache.addAll(STATIC_ASSETS)
                    .catch(function(err) {
                        console.log('[SW] Some assets failed to cache:', err);
                    });
            })
            .then(function() {
                return self.skipWaiting();
            })
    );
});

/**
 * Activate Event - Clean up old caches
 */
self.addEventListener('activate', function(event) {
    console.log('[SW] Activating service worker...');
    
    event.waitUntil(
        caches.keys()
            .then(function(cacheNames) {
                return Promise.all(
                    cacheNames
                        .filter(function(name) { return name !== CACHE_NAME; })
                        .map(function(name) {
                            console.log('[SW] Deleting old cache:', name);
                            return caches.delete(name);
                        })
                );
            })
            .then(function() {
                return self.clients.claim();
            })
    );
});

/**
 * Fetch Event - Network first, cache fallback strategy
 */
self.addEventListener('fetch', function(event) {
    var request = event.request;
    var url = new URL(request.url);
    
    // Skip non-GET requests and external resources
    if (request.method !== 'GET' || !url.origin.includes(self.location.origin)) {
        return;
    }
    
    // Skip API and dynamic routes
    if (url.pathname.startsWith('/api/') || 
        url.pathname.startsWith('/sanctum/') ||
        url.pathname.includes('/livewire/')) {
        return;
    }
    
    // Handle navigation requests
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .catch(function() {
                    return caches.match(OFFLINE_URL) || caches.match('/');
                })
        );
        return;
    }
    
    // Check if it's a static asset
    var isStatic = Object.values(CACHE_PATTERNS).some(function(pattern) {
        return pattern.test(url.pathname);
    });
    
    // Stale-while-revalidate for static assets
    if (isStatic) {
        event.respondWith(
            caches.match(request).then(function(cachedResponse) {
                var fetchPromise = fetch(request).then(function(networkResponse) {
                    if (networkResponse && networkResponse.status === 200) {
                        var responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then(function(cache) {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(function() {
                    return cachedResponse;
                });
                
                return cachedResponse || fetchPromise;
            })
        );
        return;
    }
    
    // Network first for other requests
    event.respondWith(
        fetch(request)
            .then(function(response) {
                // Cache successful responses
                if (response && response.status === 200) {
                    var responseToCache = response.clone();
                    caches.open(CACHE_NAME).then(function(cache) {
                        cache.put(request, responseToCache);
                    });
                }
                return response;
            })
            .catch(function() {
                return caches.match(request);
            })
    );
});

/**
 * Push Event - Handle push notifications
 */
self.addEventListener('push', function(event) {
    if (!event.data) {
        console.log('[SW] Push event but no data');
        return;
    }

    var data;
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'Notificare nouă',
            body: event.data.text(),
            icon: '/images/logo.png'
        };
    }

    var title = data.title || 'Omul Potrivit';
    var options = {
        body: data.body || 'Ai o notificare nouă',
        icon: data.icon || '/images/logo.png',
        badge: data.badge || '/images/badge-72x72.png',
        image: data.image || null,
        tag: data.tag || 'omulpotrivit-notification',
        renotify: data.renotify || false,
        requireInteraction: data.requireInteraction || false,
        vibrate: [200, 100, 200],
        data: {
            url: data.url || '/',
            notificationId: data.notificationId || null
        },
        actions: data.actions || [
            { action: 'open', title: 'Deschide' },
            { action: 'close', title: 'Închide' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

/**
 * Notification Click Event
 */
self.addEventListener('notificationclick', function(event) {
    var notification = event.notification;
    var action = event.action;
    var data = notification.data || {};

    notification.close();

    if (action === 'close') {
        return;
    }

    // Open associated URL
    var urlToOpen = data.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then(function(clientList) {
                // Check if window already open
                for (var i = 0; i < clientList.length; i++) {
                    var client = clientList[i];
                    if (client.url.includes(urlToOpen) && 'focus' in client) {
                        return client.focus();
                    }
                }
                // Open new window
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );

    // Mark notification as read
    if (data.notificationId) {
        fetch('/notificari/' + data.notificationId + '/citit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        }).catch(function(error) {
            console.log('[SW] Could not mark notification as read:', error);
        });
    }
});

/**
 * Notification Close Event
 */
self.addEventListener('notificationclose', function(event) {
    console.log('[SW] Notification closed:', event.notification.tag);
});

/**
 * Background Sync Event
 */
self.addEventListener('sync', function(event) {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-messages') {
        event.waitUntil(syncMessages());
    }
    
    if (event.tag === 'sync-notifications') {
        event.waitUntil(syncNotifications());
    }
});

/**
 * Sync queued messages
 */
function syncMessages() {
    return new Promise(function(resolve) {
        console.log('[SW] Syncing messages...');
        resolve();
    });
}

/**
 * Sync notifications
 */
function syncNotifications() {
    return new Promise(function(resolve) {
        console.log('[SW] Syncing notifications...');
        resolve();
    });
}

/**
 * Message Event - Communication with main thread
 */
self.addEventListener('message', function(event) {
    console.log('[SW] Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'GET_CACHE_STATS') {
        getCacheStats().then(function(stats) {
            event.ports[0].postMessage(stats);
        });
    }
    
    if (event.data && event.data.type === 'CLEAR_CACHE') {
        caches.delete(CACHE_NAME).then(function() {
            event.ports[0].postMessage({ success: true });
        });
    }
});

/**
 * Get cache statistics
 */
function getCacheStats() {
    return caches.open(CACHE_NAME).then(function(cache) {
        return cache.keys().then(function(keys) {
            return {
                cacheSize: keys.length,
                cacheName: CACHE_NAME
            };
        });
    });
}
