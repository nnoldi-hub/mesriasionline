import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import './dark-mode';
import './infinite-scroll';
import './push-notifications';
import './ajax-filters';
import './craftsmen-map';
import './compare-craftsmen';
import './touch-gestures';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// Register service worker for PWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('[PWA] Service Worker registered:', registration.scope);
                
                // Check for updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            // New update available
                            showUpdateNotification();
                        }
                    });
                });
            })
            .catch(error => {
                console.log('[PWA] Service Worker registration failed:', error);
            });
    });
}

// Show update notification
function showUpdateNotification() {
    const banner = document.createElement('div');
    banner.className = 'fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-primary-600 text-white p-4 rounded-lg shadow-lg z-50 slide-in-up';
    banner.innerHTML = `
        <div class="flex items-center justify-between">
            <span>O nouă versiune este disponibilă!</span>
            <button onclick="location.reload()" class="ml-4 px-3 py-1 bg-white text-primary-600 rounded font-medium hover:bg-gray-100 transition">
                Actualizează
            </button>
        </div>
    `;
    document.body.appendChild(banner);
}

// Lazy load images
document.addEventListener('DOMContentLoaded', () => {
    const lazyImages = document.querySelectorAll('img[loading="lazy"]');
    
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    } else {
        // Fallback for older browsers
        lazyImages.forEach(img => img.classList.add('loaded'));
    }
});

