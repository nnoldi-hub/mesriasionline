/**
 * Infinite Scroll Manager
 * Handles lazy loading of content as user scrolls
 */
class InfiniteScrollManager {
    constructor(options = {}) {
        this.container = options.container || document.querySelector('[data-infinite-scroll]');
        this.itemsWrapper = options.itemsWrapper || this.container?.querySelector('[data-items]');
        this.loadingIndicator = options.loadingIndicator || this.container?.querySelector('[data-loading]');
        this.endpoint = options.endpoint || this.container?.dataset.endpoint;
        this.threshold = options.threshold || 200; // pixels from bottom
        this.page = 1;
        this.loading = false;
        this.hasMore = true;
        this.params = options.params || {};
        
        if (this.container) {
            this.init();
        }
    }

    init() {
        this.bindEvents();
        console.log('[InfiniteScroll] Initialized', { endpoint: this.endpoint });
    }

    bindEvents() {
        window.addEventListener('scroll', this.handleScroll.bind(this), { passive: true });
        
        // Also check on resize
        window.addEventListener('resize', this.handleScroll.bind(this), { passive: true });
    }

    handleScroll() {
        if (this.loading || !this.hasMore) return;

        const scrollPosition = window.innerHeight + window.scrollY;
        const threshold = document.documentElement.scrollHeight - this.threshold;

        if (scrollPosition >= threshold) {
            this.loadMore();
        }
    }

    async loadMore() {
        if (this.loading || !this.hasMore) return;

        this.loading = true;
        this.showLoading();

        try {
            const url = new URL(this.endpoint, window.location.origin);
            url.searchParams.set('page', this.page + 1);
            
            // Add additional params
            Object.entries(this.params).forEach(([key, value]) => {
                url.searchParams.set(key, value);
            });

            const response = await fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            
            if (data.html) {
                this.appendItems(data.html);
                this.page++;
            }

            if (data.hasMore === false || data.last_page <= this.page) {
                this.hasMore = false;
                this.showEndMessage();
            }

        } catch (error) {
            console.error('[InfiniteScroll] Error loading more items:', error);
            this.showError();
        } finally {
            this.loading = false;
            this.hideLoading();
        }
    }

    appendItems(html) {
        if (this.itemsWrapper) {
            this.itemsWrapper.insertAdjacentHTML('beforeend', html);
            
            // Trigger lazy load for new images
            this.initLazyImages();
            
            // Dispatch event for other scripts
            window.dispatchEvent(new CustomEvent('infiniteScrollItemsAdded', {
                detail: { page: this.page }
            }));
        }
    }

    initLazyImages() {
        const newImages = this.itemsWrapper.querySelectorAll('img[loading="lazy"]:not(.loaded)');
        
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('loaded');
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            newImages.forEach(img => observer.observe(img));
        } else {
            newImages.forEach(img => img.classList.add('loaded'));
        }
    }

    showLoading() {
        if (this.loadingIndicator) {
            this.loadingIndicator.classList.remove('hidden');
        }
    }

    hideLoading() {
        if (this.loadingIndicator) {
            this.loadingIndicator.classList.add('hidden');
        }
    }

    showEndMessage() {
        if (this.loadingIndicator) {
            this.loadingIndicator.innerHTML = `
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <p>Ai ajuns la sfârșitul listei</p>
                </div>
            `;
            this.loadingIndicator.classList.remove('hidden');
        }
    }

    showError() {
        if (this.loadingIndicator) {
            this.loadingIndicator.innerHTML = `
                <div class="text-center py-8 text-red-500 dark:text-red-400">
                    <svg class="mx-auto w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>Eroare la încărcare</p>
                    <button onclick="window.infiniteScroll?.loadMore()" class="mt-2 text-primary-600 hover:underline">
                        Încearcă din nou
                    </button>
                </div>
            `;
            this.loadingIndicator.classList.remove('hidden');
            this.hasMore = true; // Allow retry
        }
    }

    reset() {
        this.page = 1;
        this.hasMore = true;
        this.loading = false;
        if (this.itemsWrapper) {
            this.itemsWrapper.innerHTML = '';
        }
    }

    updateParams(newParams) {
        this.params = { ...this.params, ...newParams };
        this.reset();
        this.loadMore();
    }

    destroy() {
        window.removeEventListener('scroll', this.handleScroll);
        window.removeEventListener('resize', this.handleScroll);
    }
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('[data-infinite-scroll]');
    if (container) {
        window.infiniteScroll = new InfiniteScrollManager();
    }
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = InfiniteScrollManager;
}
