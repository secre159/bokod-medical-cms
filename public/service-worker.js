/**
 * Bokod Medical CMS - Service Worker
 * Provides offline capabilities and performance improvements through intelligent caching
 */

const CACHE_NAME = 'bokod-cms-v1.0.0';
const OFFLINE_URL = '/offline.html';

// Resources to cache immediately
const PRECACHE_RESOURCES = [
    '/',
    '/login',
    '/css/performance.css',
    '/js/loading-optimizer.js',
    '/vendor/adminlte/dist/css/adminlte.min.css',
    '/vendor/adminlte/dist/js/adminlte.min.js',
    '/vendor/fontawesome-free/css/all.min.css',
    '/offline.html'
];

// Install event - precache critical resources
self.addEventListener('install', event => {
    console.log('🔧 Service Worker installing...');
    
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('📦 Precaching resources');
                return cache.addAll(PRECACHE_RESOURCES.map(url => new Request(url, {
                    credentials: 'same-origin'
                })));
            })
            .then(() => {
                console.log('✅ Precaching complete');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('❌ Precaching failed:', error);
            })
    );
});

// Activate event - cleanup old caches
self.addEventListener('activate', event => {
    console.log('🚀 Service Worker activating...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames
                        .filter(cacheName => cacheName !== CACHE_NAME)
                        .map(cacheName => {
                            console.log('🗑️ Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        })
                );
            })
            .then(() => {
                console.log('✅ Old caches cleaned up');
                return self.clients.claim();
            })
    );
});

// Fetch event - intelligent caching strategy
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip Chrome extensions and other non-http(s) requests
    if (!url.protocol.startsWith('http')) {
        return;
    }
    
    event.respondWith(handleRequest(request));
});

/**
 * Handle fetch requests with intelligent caching
 */
async function handleRequest(request) {
    const url = new URL(request.url);
    
    try {
        // Strategy 1: Network First for API calls and dynamic content
        if (url.pathname.startsWith('/api/') || 
            url.pathname.includes('dashboard') ||
            url.pathname.includes('patient') ||
            url.pathname.includes('appointment')) {
            return await networkFirstStrategy(request);
        }
        
        // Strategy 2: Cache First for static assets
        if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|webp|svg|woff2?|ttf|eot)$/)) {
            return await cacheFirstStrategy(request);
        }
        
        // Strategy 3: Stale While Revalidate for pages
        return await staleWhileRevalidateStrategy(request);
        
    } catch (error) {
        console.warn('Request failed:', request.url, error);
        return await handleOffline(request);
    }
}

/**
 * Network First Strategy - Try network, fallback to cache
 */
async function networkFirstStrategy(request) {
    const cache = await caches.open(CACHE_NAME);
    
    try {
        // Try network first
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            // Cache successful responses
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // Network failed, try cache
        console.log('📱 Network failed, trying cache for:', request.url);
        const cachedResponse = await cache.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        throw error;
    }
}

/**
 * Cache First Strategy - Try cache, fallback to network
 */
async function cacheFirstStrategy(request) {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
        // Return cached version immediately
        updateCacheInBackground(request, cache);
        return cachedResponse;
    }
    
    // Not in cache, fetch from network
    const networkResponse = await fetch(request);
    
    if (networkResponse.ok) {
        cache.put(request, networkResponse.clone());
    }
    
    return networkResponse;
}

/**
 * Stale While Revalidate Strategy - Return cache immediately, update in background
 */
async function staleWhileRevalidateStrategy(request) {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);
    
    // Fetch from network in background
    const fetchPromise = fetch(request).then(networkResponse => {
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    });
    
    // Return cached version immediately if available
    if (cachedResponse) {
        return cachedResponse;
    }
    
    // No cache, wait for network
    return await fetchPromise;
}

/**
 * Update cache in background without blocking response
 */
function updateCacheInBackground(request, cache) {
    fetch(request)
        .then(response => {
            if (response.ok) {
                cache.put(request, response);
            }
        })
        .catch(() => {
            // Silent fail for background updates
        });
}

/**
 * Handle offline scenarios
 */
async function handleOffline(request) {
    const cache = await caches.open(CACHE_NAME);
    
    // Try to serve from cache first
    const cachedResponse = await cache.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    // For HTML requests, show offline page
    if (request.destination === 'document') {
        const offlinePage = await cache.match(OFFLINE_URL);
        if (offlinePage) {
            return offlinePage;
        }
    }
    
    // For other resources, return a basic offline response
    return new Response('Offline - Content not available', {
        status: 503,
        statusText: 'Service Unavailable',
        headers: {
            'Content-Type': 'text/plain'
        }
    });
}

/**
 * Background sync for form submissions when back online
 */
self.addEventListener('sync', event => {
    if (event.tag === 'background-sync') {
        console.log('🔄 Background sync triggered');
        event.waitUntil(processBackgroundSync());
    }
});

async function processBackgroundSync() {
    // Handle queued form submissions or other background tasks
    console.log('📤 Processing background sync tasks...');
    // Implementation would depend on your specific needs
}

/**
 * Handle push notifications (if needed)
 */
self.addEventListener('push', event => {
    if (event.data) {
        const options = {
            body: event.data.text(),
            icon: '/icons/icon-192x192.png',
            badge: '/icons/badge-72x72.png',
            vibrate: [100, 50, 100],
            data: {
                dateOfArrival: Date.now(),
                primaryKey: 1
            },
            actions: [
                {
                    action: 'explore',
                    title: 'View Details',
                    icon: '/icons/checkmark.png'
                },
                {
                    action: 'close',
                    title: 'Close',
                    icon: '/icons/xmark.png'
                }
            ]
        };
        
        event.waitUntil(
            self.registration.showNotification('Bokod Medical CMS', options)
        );
    }
});

// Log service worker status
console.log('🛠️ Bokod Medical CMS Service Worker loaded');