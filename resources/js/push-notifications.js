**
 * Push Notifications Manager
 * Omul Potrivit - Platforma Meseriași
 */

class PushNotificationManager {
    constructor() {
        this.vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]')?.content;
        this.isSupported = 'serviceWorker' in navigator && 'PushManager' in window;
        this.registration = null;
        this.subscription = null;
    }

    /**
     * Inițializează managerul de notificări push
     */
    async init() {
        if (!this.isSupported) {
            console.log('Push notifications nu sunt suportate în acest browser.');
            return false;
        }

        if (!this.vapidPublicKey) {
            console.log('Cheia VAPID publică nu este configurată.');
            return false;
        }

        try {
            // Înregistrează service worker-ul
            this.registration = await navigator.serviceWorker.register('/sw.js');
            console.log('Service Worker înregistrat:', this.registration);

            // Verifică starea curentă a subscripției
            this.subscription = await this.registration.pushManager.getSubscription();
            
            this.updateUI();
            return true;
        } catch (error) {
            console.error('Eroare la inițializarea push notifications:', error);
            return false;
        }
    }

    /**
     * Verifică dacă utilizatorul este deja abonat
     */
    isSubscribed() {
        return this.subscription !== null;
    }

    /**
     * Solicită permisiunea și abonează utilizatorul
     */
    async subscribe() {
        if (!this.isSupported || !this.registration) {
            throw new Error('Push notifications nu sunt disponibile.');
        }

        // Solicită permisiunea
        const permission = await Notification.requestPermission();
        
        if (permission !== 'granted') {
            throw new Error('Permisiunea pentru notificări a fost refuzată.');
        }

        try {
            // Creează subscripția
            this.subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(this.vapidPublicKey)
            });

            // Trimite subscripția la server
            await this.sendSubscriptionToServer(this.subscription);
            
            this.updateUI();
            return this.subscription;
        } catch (error) {
            console.error('Eroare la abonare:', error);
            throw error;
        }
    }

    /**
     * Dezabonează utilizatorul
     */
    async unsubscribe() {
        if (!this.subscription) {
            return;
        }

        try {
            // Șterge subscripția de pe server
            await this.removeSubscriptionFromServer(this.subscription);
            
            // Dezabonează local
            await this.subscription.unsubscribe();
            this.subscription = null;
            
            this.updateUI();
        } catch (error) {
            console.error('Eroare la dezabonare:', error);
            throw error;
        }
    }

    /**
     * Trimite subscripția la server
     */
    async sendSubscriptionToServer(subscription) {
        const response = await fetch('/push-subscriptions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(subscription.toJSON())
        });

        if (!response.ok) {
            throw new Error('Eroare la salvarea subscripției pe server.');
        }

        return response.json();
    }

    /**
     * Șterge subscripția de pe server
     */
    async removeSubscriptionFromServer(subscription) {
        const response = await fetch('/push-subscriptions', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ endpoint: subscription.endpoint })
        });

        if (!response.ok) {
            throw new Error('Eroare la ștergerea subscripției de pe server.');
        }

        return response.json();
    }

    /**
     * Actualizează UI-ul bazat pe starea subscripției
     */
    updateUI() {
        const subscribeBtn = document.getElementById('push-subscribe-btn');
        const unsubscribeBtn = document.getElementById('push-unsubscribe-btn');
        const statusText = document.getElementById('push-status');
        
        if (this.isSubscribed()) {
            if (subscribeBtn) subscribeBtn.classList.add('d-none');
            if (unsubscribeBtn) unsubscribeBtn.classList.remove('d-none');
            if (statusText) {
                statusText.textContent = 'Notificările push sunt activate';
                statusText.classList.remove('text-muted');
                statusText.classList.add('text-success');
            }
        } else {
            if (subscribeBtn) subscribeBtn.classList.remove('d-none');
            if (unsubscribeBtn) unsubscribeBtn.classList.add('d-none');
            if (statusText) {
                statusText.textContent = 'Notificările push sunt dezactivate';
                statusText.classList.remove('text-success');
                statusText.classList.add('text-muted');
            }
        }

        // Toggle pentru checkbox/switch
        const pushToggle = document.getElementById('push-toggle');
        if (pushToggle) {
            pushToggle.checked = this.isSubscribed();
        }
    }

    /**
     * Convertește cheia VAPID în format Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    /**
     * Toggle pentru subscribe/unsubscribe
     */
    async toggle() {
        if (this.isSubscribed()) {
            await this.unsubscribe();
        } else {
            await this.subscribe();
        }
    }
}

// Inițializare la încărcarea paginii
document.addEventListener('DOMContentLoaded', async function() {
    // Verifică dacă utilizatorul este autentificat
    const isAuthenticated = document.querySelector('meta[name="user-authenticated"]')?.content === 'true';
    
    if (!isAuthenticated) {
        return;
    }

    const pushManager = new PushNotificationManager();
    const initialized = await pushManager.init();

    if (!initialized) {
        // Ascunde elementele UI pentru push notifications
        document.querySelectorAll('.push-notification-controls').forEach(el => {
            el.style.display = 'none';
        });
        return;
    }

    // Event listeners pentru butoane
    const subscribeBtn = document.getElementById('push-subscribe-btn');
    if (subscribeBtn) {
        subscribeBtn.addEventListener('click', async function() {
            try {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Se activează...';
                await pushManager.subscribe();
                showToast('Notificările push au fost activate!', 'success');
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-bell me-1"></i> Activează Notificări Push';
            }
        });
    }

    const unsubscribeBtn = document.getElementById('push-unsubscribe-btn');
    if (unsubscribeBtn) {
        unsubscribeBtn.addEventListener('click', async function() {
            try {
                this.disabled = true;
                await pushManager.unsubscribe();
                showToast('Notificările push au fost dezactivate.', 'info');
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                this.disabled = false;
            }
        });
    }

    // Toggle switch
    const pushToggle = document.getElementById('push-toggle');
    if (pushToggle) {
        pushToggle.addEventListener('change', async function() {
            try {
                this.disabled = true;
                await pushManager.toggle();
                const message = pushManager.isSubscribed() 
                    ? 'Notificările push au fost activate!' 
                    : 'Notificările push au fost dezactivate.';
                showToast(message, pushManager.isSubscribed() ? 'success' : 'info');
            } catch (error) {
                this.checked = !this.checked;
                showToast(error.message, 'error');
            } finally {
                this.disabled = false;
            }
        });
    }

    // Expune managerul global pentru debugging
    window.pushNotificationManager = pushManager;
});

/**
 * Helper pentru a afișa toast notifications
 */
function showToast(message, type = 'info') {
    // Verifică dacă există un sistem de toast în aplicație
    if (typeof Toastify !== 'undefined') {
        Toastify({
            text: message,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'
        }).showToast();
    } else if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
        // Folosește Bootstrap toast
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} border-0`;
        toastEl.setAttribute('role', 'alert');
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    } else {
        // Fallback la alert
        alert(message);
    }
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '9999';
    document.body.appendChild(container);
    return container;
}
