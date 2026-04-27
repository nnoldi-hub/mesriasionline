/**
 * AJAX Filters Manager
 * Handles real-time filtering of craftsmen without page reload
 */

class AjaxFiltersManager {
    constructor(options = {}) {
        this.container = document.getElementById(options.containerId || 'craftsmen-grid');
        this.form = document.getElementById(options.formId || 'searchForm');
        this.resultsCount = document.getElementById(options.resultsCountId || 'results-count');
        this.loadingOverlay = null;
        this.debounceTimer = null;
        this.debounceDelay = options.debounceDelay || 300;
        this.apiEndpoint = options.apiEndpoint || '/api/v1/craftsmen/filter';
        this.initialized = false;
        
        this.init();
    }

    init() {
        if (!this.form || !this.container) {
            console.log('AJAX Filters: Required elements not found');
            return;
        }

        this.createLoadingOverlay();
        this.bindEvents();
        this.initialized = true;
        console.log('AJAX Filters initialized');
    }

    createLoadingOverlay() {
        this.loadingOverlay = document.createElement('div');
        this.loadingOverlay.className = 'ajax-loading-overlay hidden';
        this.loadingOverlay.innerHTML = `
            <div class="flex items-center justify-center h-full">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary-600 border-t-transparent"></div>
            </div>
        `;
        this.container.parentElement.style.position = 'relative';
        this.container.parentElement.appendChild(this.loadingOverlay);
    }

    bindEvents() {
        // Bind to all form inputs
        const inputs = this.form.querySelectorAll('select, input[type="text"], input[type="checkbox"]');
        
        inputs.forEach(input => {
            const eventType = input.type === 'checkbox' ? 'change' : 'input';
            input.addEventListener(eventType, (e) => {
                // Don't auto-submit on text input - wait for user to stop typing
                if (input.type === 'text') {
                    this.debounce(() => this.fetchResults(), this.debounceDelay);
                } else {
                    this.fetchResults();
                }
            });
        });

        // Override form submit
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.fetchResults();
        });

        // Handle browser back/forward
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.filters) {
                this.applyFiltersFromState(e.state.filters);
                this.fetchResults(false); // Don't push state again
            }
        });
    }

    debounce(func, delay) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(func, delay);
    }

    showLoading() {
        this.loadingOverlay?.classList.remove('hidden');
        this.container.style.opacity = '0.5';
    }

    hideLoading() {
        this.loadingOverlay?.classList.add('hidden');
        this.container.style.opacity = '1';
    }

    getFilters() {
        const formData = new FormData(this.form);
        const filters = {};
        
        for (let [key, value] of formData.entries()) {
            if (value) {
                filters[key] = value;
            }
        }
        
        return filters;
    }

    async fetchResults(pushState = true) {
        const filters = this.getFilters();
        
        this.showLoading();

        try {
            const params = new URLSearchParams(filters);
            const response = await fetch(`${this.apiEndpoint}?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            this.renderResults(data);
            this.updateURL(filters, pushState);
            this.updateResultsCount(data.total || data.craftsmen?.length || 0);

            // Dispatch event for other components (like map)
            window.dispatchEvent(new CustomEvent('craftsmenFiltered', { 
                detail: { craftsmen: data.craftsmen || data.data, filters } 
            }));

        } catch (error) {
            console.error('Error fetching results:', error);
            this.showError('Eroare la încărcarea rezultatelor. Încercați din nou.');
        } finally {
            this.hideLoading();
        }
    }

    renderResults(data) {
        const craftsmen = data.craftsmen || data.data || [];
        
        if (craftsmen.length === 0) {
            this.container.innerHTML = this.renderEmptyState();
            return;
        }

        this.container.innerHTML = craftsmen.map(craftsman => this.renderCard(craftsman)).join('');
        
        // Re-initialize any JS needed for cards (like compare checkboxes)
        this.initCardInteractions();
    }

    renderCard(craftsman) {
        const ratingStars = this.generateStars(craftsman.rating || 0);
        const badges = this.generateBadges(craftsman);
        const gallery = this.generateGallery(craftsman.gallery || []);
        
        return `
            <div class="craftsman-card bg-white rounded-lg shadow-md hover:shadow-xl transition overflow-hidden relative" data-craftsman-id="${craftsman.id}">
                ${badges}
                ${gallery}
                
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center">
                            <img src="${craftsman.profile_photo || '/images/default-avatar.png'}" 
                                 alt="${craftsman.name}" 
                                 class="w-14 h-14 rounded-full object-cover mr-4"
                                 onerror="this.src='/images/default-avatar.png'">
                            <div>
                                <h3 class="font-semibold text-lg text-gray-900">${craftsman.name}</h3>
                                <p class="text-gray-600 text-sm">${craftsman.specialization || craftsman.category || ''}</p>
                            </div>
                        </div>
                        <label class="compare-checkbox cursor-pointer" title="Adaugă la comparație">
                            <input type="checkbox" 
                                   class="compare-select hidden" 
                                   data-id="${craftsman.id}"
                                   data-name="${craftsman.name}">
                            <svg class="w-6 h-6 text-gray-400 hover:text-primary-600 transition compare-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </label>
                    </div>
                    
                    <div class="mt-3 flex items-center gap-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            ${ratingStars}
                            <span class="ml-1">${(craftsman.rating || 0).toFixed(1)}</span>
                            <span class="text-gray-400 ml-1">(${craftsman.reviews_count || 0})</span>
                        </div>
                        ${craftsman.distance ? `
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                </svg>
                                ${craftsman.distance} km
                            </div>
                        ` : ''}
                    </div>
                    
                    ${craftsman.location ? `
                        <p class="mt-2 text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            ${craftsman.location}
                        </p>
                    ` : ''}
                    
                    <div class="mt-4 flex items-center gap-2">
                        <a href="/meserias/${craftsman.slug}" 
                           class="flex-1 bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-lg transition text-sm font-medium">
                            Vezi profil
                        </a>
                        <button type="button" 
                                class="p-2 text-gray-400 hover:text-red-500 transition favorite-btn"
                                data-craftsman-id="${craftsman.id}"
                                title="Adaugă la favorite">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }

    generateStars(rating) {
        const fullStars = Math.floor(rating);
        const hasHalf = rating % 1 >= 0.5;
        let stars = '';
        
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                stars += '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else if (i === fullStars && hasHalf) {
                stars += '<svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><defs><linearGradient id="halfStar"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#D1D5DB"/></linearGradient></defs><path fill="url(#halfStar)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            } else {
                stars += '<svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
            }
        }
        
        return `<div class="flex">${stars}</div>`;
    }

    generateBadges(craftsman) {
        let badges = '<div class="absolute top-3 right-3 flex flex-col gap-1 z-10">';
        
        if (craftsman.is_featured) {
            badges += `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Top
                </span>`;
        }
        
        if (craftsman.is_verified) {
            badges += `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verificat
                </span>`;
        }
        
        badges += '</div>';
        return badges;
    }

    generateGallery(gallery) {
        if (!gallery || gallery.length === 0) {
            return '';
        }

        const images = gallery.slice(0, 3);
        return `
            <div class="h-32 bg-gray-100 overflow-hidden">
                <div class="flex h-full">
                    ${images.map((photo, index) => `
                        <div class="flex-1 ${index > 0 ? 'border-l border-white' : ''}">
                            <img src="${photo.image_path || photo.url || photo}" 
                                 alt="Portofoliu" 
                                 class="w-full h-full object-cover">
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    renderEmptyState() {
        return `
            <div class="col-span-full py-16 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Niciun meseriaș găsit</h3>
                <p class="text-gray-500 mb-4">Încercați să modificați filtrele de căutare.</p>
                <button type="button" 
                        onclick="ajaxFilters.resetFilters()" 
                        class="text-primary-600 hover:text-primary-700 font-medium">
                    Resetați filtrele
                </button>
            </div>
        `;
    }

    showError(message) {
        this.container.innerHTML = `
            <div class="col-span-full py-16 text-center">
                <svg class="w-16 h-16 mx-auto text-red-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Eroare</h3>
                <p class="text-gray-500">${message}</p>
            </div>
        `;
    }

    updateURL(filters, pushState = true) {
        const params = new URLSearchParams(filters);
        const newURL = `${window.location.pathname}?${params.toString()}`;
        
        if (pushState) {
            window.history.pushState({ filters }, '', newURL);
        }
    }

    updateResultsCount(count) {
        if (this.resultsCount) {
            this.resultsCount.textContent = `${count} meseriași găsiți`;
        }
    }

    applyFiltersFromState(filters) {
        Object.entries(filters).forEach(([key, value]) => {
            const input = this.form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = value === '1' || value === 'true';
                } else {
                    input.value = value;
                }
            }
        });
    }

    resetFilters() {
        this.form.reset();
        this.fetchResults();
    }

    initCardInteractions() {
        // Re-bind compare checkbox handlers
        if (window.compareManager) {
            window.compareManager.bindCheckboxes();
        }

        // Re-bind favorite buttons
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const craftsmanId = btn.dataset.craftsmanId;
                this.toggleFavorite(craftsmanId, btn);
            });
        });
    }

    async toggleFavorite(craftsmanId, button) {
        try {
            const response = await fetch(`/favorites/${craftsmanId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            // Toggle visual state
            const svg = button.querySelector('svg');
            if (data.is_favorited) {
                svg.classList.add('text-red-500', 'fill-current');
                svg.classList.remove('text-gray-400');
            } else {
                svg.classList.remove('text-red-500', 'fill-current');
                svg.classList.add('text-gray-400');
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
        }
    }
}

// CSS for loading overlay
const style = document.createElement('style');
style.textContent = `
    .ajax-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 20;
    }
    .ajax-loading-overlay.hidden {
        display: none;
    }
`;
document.head.appendChild(style);

// Auto-initialize
let ajaxFilters;
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on home page with craftsmen grid
    if (document.getElementById('craftsmen-grid') && document.getElementById('searchForm')) {
        ajaxFilters = new AjaxFiltersManager({
            containerId: 'craftsmen-grid',
            formId: 'searchForm',
            resultsCountId: 'results-count',
            apiEndpoint: '/api/v1/craftsmen/filter'
        });
        window.ajaxFilters = ajaxFilters;
    }
});

export default AjaxFiltersManager;
