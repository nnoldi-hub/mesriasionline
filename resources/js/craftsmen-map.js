/**
 * Interactive Map Manager
 * Displays craftsmen on a Leaflet/OpenStreetMap map
 */

class CraftsmenMapManager {
    constructor(options = {}) {
        this.mapContainerId = options.containerId || 'craftsmen-map';
        this.map = null;
        this.markers = [];
        this.markerCluster = null;
        this.defaultCenter = options.center || [45.9432, 24.9668]; // Romania center
        this.defaultZoom = options.zoom || 7;
        this.craftsmen = [];
        this.userMarker = null;
        this.selectedCraftsman = null;
        
        this.icons = {
            default: null,
            featured: null,
            verified: null,
            user: null
        };

        this.init();
    }

    async init() {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            await this.loadLeaflet();
        }

        const container = document.getElementById(this.mapContainerId);
        if (!container) {
            console.log('Map container not found');
            return;
        }

        this.createIcons();
        this.initMap();
        this.bindEvents();
        
        console.log('Craftsmen Map initialized');
    }

    async loadLeaflet() {
        return new Promise((resolve, reject) => {
            // Load CSS
            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            css.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
            css.crossOrigin = '';
            document.head.appendChild(css);

            // Load Leaflet JS
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
            script.crossOrigin = '';
            script.onload = () => {
                // Load MarkerCluster plugin
                const clusterCss = document.createElement('link');
                clusterCss.rel = 'stylesheet';
                clusterCss.href = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css';
                document.head.appendChild(clusterCss);

                const clusterCss2 = document.createElement('link');
                clusterCss2.rel = 'stylesheet';
                clusterCss2.href = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css';
                document.head.appendChild(clusterCss2);

                const clusterScript = document.createElement('script');
                clusterScript.src = 'https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js';
                clusterScript.onload = resolve;
                clusterScript.onerror = reject;
                document.head.appendChild(clusterScript);
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    createIcons() {
        const iconSize = [32, 42];
        const iconAnchor = [16, 42];
        const popupAnchor = [0, -42];

        // Default marker
        this.icons.default = L.divIcon({
            className: 'craftsman-marker',
            html: `
                <div class="marker-pin bg-primary-600">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
            `,
            iconSize: iconSize,
            iconAnchor: iconAnchor,
            popupAnchor: popupAnchor
        });

        // Featured marker
        this.icons.featured = L.divIcon({
            className: 'craftsman-marker featured',
            html: `
                <div class="marker-pin bg-yellow-500">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
            `,
            iconSize: iconSize,
            iconAnchor: iconAnchor,
            popupAnchor: popupAnchor
        });

        // Verified marker
        this.icons.verified = L.divIcon({
            className: 'craftsman-marker verified',
            html: `
                <div class="marker-pin bg-blue-600">
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            `,
            iconSize: iconSize,
            iconAnchor: iconAnchor,
            popupAnchor: popupAnchor
        });

        // User location marker
        this.icons.user = L.divIcon({
            className: 'user-marker',
            html: `
                <div class="user-pin">
                    <div class="pulse-ring"></div>
                    <div class="center-dot"></div>
                </div>
            `,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        });
    }

    initMap() {
        const container = document.getElementById(this.mapContainerId);
        
        this.map = L.map(this.mapContainerId, {
            center: this.defaultCenter,
            zoom: this.defaultZoom,
            scrollWheelZoom: true,
            zoomControl: true
        });

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(this.map);

        // Initialize marker cluster group
        if (typeof L.markerClusterGroup !== 'undefined') {
            this.markerCluster = L.markerClusterGroup({
                maxClusterRadius: 50,
                spiderfyOnMaxZoom: true,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true
            });
            this.map.addLayer(this.markerCluster);
        }

        // Add custom controls
        this.addCustomControls();
    }

    addCustomControls() {
        // Custom locate control
        const locateControl = L.control({ position: 'topleft' });
        locateControl.onAdd = () => {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = `
                <a href="#" class="locate-btn" title="Locația mea" style="display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; background: white;">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </a>
            `;
            div.querySelector('.locate-btn').addEventListener('click', (e) => {
                e.preventDefault();
                this.locateUser();
            });
            return div;
        };
        locateControl.addTo(this.map);

        // Toggle view control (list/map)
        const toggleControl = L.control({ position: 'topright' });
        toggleControl.onAdd = () => {
            const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
            div.innerHTML = `
                <a href="#" class="toggle-view-btn" title="Înapoi la listă" style="display: flex; align-items: center; justify-content: center; width: 34px; height: 34px; background: white;">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </a>
            `;
            div.querySelector('.toggle-view-btn').addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleView();
            });
            return div;
        };
        toggleControl.addTo(this.map);
    }

    bindEvents() {
        // Listen for filtered craftsmen from AJAX filters
        window.addEventListener('craftsmenFiltered', (e) => {
            this.updateMarkers(e.detail.craftsmen);
        });

        // Listen for map toggle button
        document.querySelectorAll('[data-toggle-map]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleView();
            });
        });
    }

    async loadCraftsmen(filters = {}) {
        try {
            const params = new URLSearchParams(filters);
            params.append('with_coordinates', '1');
            
            const response = await fetch(`/api/v1/craftsmen?${params.toString()}`);
            const data = await response.json();
            
            this.craftsmen = data.craftsmen || data.data || [];
            this.updateMarkers(this.craftsmen);
        } catch (error) {
            console.error('Error loading craftsmen for map:', error);
        }
    }

    updateMarkers(craftsmen) {
        this.clearMarkers();
        
        if (!craftsmen || craftsmen.length === 0) {
            return;
        }

        const bounds = [];

        craftsmen.forEach(craftsman => {
            if (craftsman.latitude && craftsman.longitude) {
                const marker = this.createMarker(craftsman);
                if (marker) {
                    if (this.markerCluster) {
                        this.markerCluster.addLayer(marker);
                    } else {
                        marker.addTo(this.map);
                    }
                    this.markers.push(marker);
                    bounds.push([craftsman.latitude, craftsman.longitude]);
                }
            }
        });

        // Fit map to show all markers
        if (bounds.length > 0) {
            this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 13 });
        }
    }

    createMarker(craftsman) {
        const lat = parseFloat(craftsman.latitude);
        const lng = parseFloat(craftsman.longitude);
        
        if (isNaN(lat) || isNaN(lng)) {
            return null;
        }

        // Choose icon based on craftsman status
        let icon = this.icons.default;
        if (craftsman.is_featured) {
            icon = this.icons.featured;
        } else if (craftsman.is_verified) {
            icon = this.icons.verified;
        }

        const marker = L.marker([lat, lng], { icon });
        
        // Create popup content
        const popupContent = this.createPopupContent(craftsman);
        marker.bindPopup(popupContent, {
            maxWidth: 300,
            className: 'craftsman-popup'
        });

        // Store craftsman data on marker
        marker.craftsmanData = craftsman;

        // Events
        marker.on('click', () => {
            this.selectedCraftsman = craftsman;
        });

        return marker;
    }

    createPopupContent(craftsman) {
        const rating = craftsman.rating || craftsman.reviews_avg_rating || 0;
        const reviewsCount = craftsman.reviews_count || 0;
        
        return `
            <div class="craftsman-popup-content">
                <div class="flex items-center gap-3 mb-2">
                    <img src="${craftsman.profile_photo || '/images/default-avatar.png'}" 
                         alt="${craftsman.name}"
                         class="w-12 h-12 rounded-full object-cover"
                         onerror="this.src='/images/default-avatar.png'">
                    <div>
                        <h4 class="font-semibold text-gray-900">${craftsman.name}</h4>
                        <p class="text-sm text-gray-600">${craftsman.specialization || craftsman.category || ''}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-sm mb-2">
                    <div class="flex items-center text-yellow-500">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        <span class="ml-1">${rating.toFixed(1)}</span>
                    </div>
                    <span class="text-gray-400">(${reviewsCount} recenzii)</span>
                    ${craftsman.is_verified ? '<span class="text-blue-600 text-xs font-medium">✓ Verificat</span>' : ''}
                </div>
                ${craftsman.location ? `
                    <p class="text-sm text-gray-500 mb-3">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        ${typeof craftsman.location === 'object' ? craftsman.location.name : craftsman.location}
                    </p>
                ` : ''}
                <a href="/meserias/${craftsman.slug}" 
                   class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded text-sm font-medium transition">
                    Vezi profil
                </a>
            </div>
        `;
    }

    clearMarkers() {
        if (this.markerCluster) {
            this.markerCluster.clearLayers();
        }
        this.markers.forEach(marker => marker.remove());
        this.markers = [];
    }

    locateUser() {
        if (!navigator.geolocation) {
            alert('Geolocalizarea nu este suportată de browserul tău.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Remove existing user marker
                if (this.userMarker) {
                    this.userMarker.remove();
                }

                // Add user marker
                this.userMarker = L.marker([lat, lng], { icon: this.icons.user })
                    .addTo(this.map)
                    .bindPopup('Locația ta')
                    .openPopup();

                // Center map on user location
                this.map.setView([lat, lng], 12);

                // Update hidden form fields if they exist
                const latInput = document.getElementById('userLat');
                const lngInput = document.getElementById('userLng');
                if (latInput) latInput.value = lat;
                if (lngInput) lngInput.value = lng;

                // Trigger filter update
                if (window.ajaxFilters) {
                    window.ajaxFilters.fetchResults();
                }
            },
            (error) => {
                console.error('Geolocation error:', error);
                alert('Nu am putut determina locația ta. Verifică setările browserului.');
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    toggleView() {
        const mapContainer = document.getElementById('map-container');
        const listContainer = document.getElementById('list-container');
        const mapToggle = document.querySelector('[data-toggle-map]');

        if (mapContainer && listContainer) {
            const isMapVisible = !mapContainer.classList.contains('hidden');
            
            if (isMapVisible) {
                mapContainer.classList.add('hidden');
                listContainer.classList.remove('hidden');
                if (mapToggle) {
                    mapToggle.innerHTML = `
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                        </svg>
                        Arată harta
                    `;
                }
            } else {
                listContainer.classList.add('hidden');
                mapContainer.classList.remove('hidden');
                if (mapToggle) {
                    mapToggle.innerHTML = `
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        Arată lista
                    `;
                }
                // Invalidate map size when showing
                setTimeout(() => this.map?.invalidateSize(), 100);
            }
        }
    }

    show() {
        const container = document.getElementById(this.mapContainerId);
        if (container) {
            container.style.display = 'block';
            this.map?.invalidateSize();
        }
    }

    hide() {
        const container = document.getElementById(this.mapContainerId);
        if (container) {
            container.style.display = 'none';
        }
    }

    flyTo(lat, lng, zoom = 15) {
        this.map?.flyTo([lat, lng], zoom);
    }

    highlightCraftsman(craftsmanId) {
        const marker = this.markers.find(m => m.craftsmanData?.id === craftsmanId);
        if (marker) {
            this.map.flyTo(marker.getLatLng(), 14);
            marker.openPopup();
        }
    }
}

// Add map marker styles
const mapStyles = document.createElement('style');
mapStyles.textContent = `
    .craftsman-marker .marker-pin {
        width: 32px;
        height: 42px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-bottom: 10px;
    }
    .craftsman-marker .marker-pin::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-top: 10px solid currentColor;
    }
    .craftsman-marker .marker-pin.bg-primary-600::after { border-top-color: #065F46; }
    .craftsman-marker .marker-pin.bg-yellow-500::after { border-top-color: #EAB308; }
    .craftsman-marker .marker-pin.bg-blue-600::after { border-top-color: #2563EB; }
    
    .craftsman-marker .marker-pin {
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
    }
    .craftsman-marker .marker-pin svg {
        transform: rotate(45deg);
    }
    
    .user-marker .user-pin {
        position: relative;
        width: 24px;
        height: 24px;
    }
    .user-marker .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.3);
        animation: pulse-ring 1.5s ease-out infinite;
    }
    .user-marker .center-dot {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 12px;
        height: 12px;
        background: #3B82F6;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    @keyframes pulse-ring {
        0% { transform: scale(0.5); opacity: 1; }
        100% { transform: scale(2); opacity: 0; }
    }
    
    .craftsman-popup .leaflet-popup-content {
        margin: 12px;
        min-width: 200px;
    }
    .craftsman-popup-content img {
        border: 2px solid #f3f4f6;
    }
    
    #craftsmen-map {
        height: 500px;
        border-radius: 8px;
        z-index: 1;
    }
    
    @media (max-width: 768px) {
        #craftsmen-map {
            height: 400px;
        }
    }
`;
document.head.appendChild(mapStyles);

// Export for module usage
let craftsmenMap;
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('craftsmen-map')) {
        craftsmenMap = new CraftsmenMapManager({
            containerId: 'craftsmen-map'
        });
        window.craftsmenMap = craftsmenMap;
    }
});

export default CraftsmenMapManager;
