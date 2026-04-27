<div class="map-container" {{ $attributes->merge(['class' => 'rounded-lg overflow-hidden']) }}>
    <div id="{{ $mapId }}" class="w-full" style="min-height: {{ $height }};"></div>
</div>

@once
@push('scripts')
<script>
class GoogleMapComponent {
    constructor(mapId, config = {}) {
        this.mapId = mapId;
        this.map = null;
        this.markers = [];
        this.infoWindow = null;
        this.config = {
            center: config.center || { lat: 45.9432, lng: 24.9668 }, // Romania center
            zoom: config.zoom || 7,
            markers: config.markers || [],
            showRadius: config.showRadius || false,
            radius: config.radius || 50, // km
            interactive: config.interactive !== false,
            cluster: config.cluster || false,
            ...config
        };

        this.init();
    }

    init() {
        if (typeof google === 'undefined') {
            console.error('Google Maps API not loaded');
            return;
        }

        this.initMap();
        this.addMarkers(this.config.markers);
        
        if (this.config.showRadius) {
            this.drawRadius(this.config.center, this.config.radius);
        }
    }

    initMap() {
        const mapElement = document.getElementById(this.mapId);
        if (!mapElement) return;

        this.map = new google.maps.Map(mapElement, {
            center: this.config.center,
            zoom: this.config.zoom,
            mapTypeControl: this.config.interactive,
            streetViewControl: this.config.interactive,
            fullscreenControl: this.config.interactive,
            zoomControl: this.config.interactive,
            styles: this.getMapStyles(),
        });

        this.infoWindow = new google.maps.InfoWindow();
    }

    addMarkers(markersData) {
        markersData.forEach(data => this.addMarker(data));
        
        if (markersData.length > 0) {
            this.fitBounds();
        }
    }

    addMarker(data) {
        if (!this.map) return;

        const marker = new google.maps.Marker({
            position: { lat: parseFloat(data.lat), lng: parseFloat(data.lng) },
            map: this.map,
            title: data.name,
            icon: this.getMarkerIcon(data),
        });

        if (data.content) {
            marker.addListener('click', () => {
                this.infoWindow.setContent(this.getInfoWindowContent(data));
                this.infoWindow.open(this.map, marker);
            });
        }

        this.markers.push(marker);
    }

    getMarkerIcon(data) {
        // Custom marker with craftsman avatar or default icon
        if (data.avatar) {
            return {
                url: data.avatar,
                scaledSize: new google.maps.Size(40, 40),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(20, 40),
            };
        }
        return null; // Use default marker
    }

    getInfoWindowContent(data) {
        return `
            <div class="p-3 max-w-xs">
                <div class="flex items-center space-x-3 mb-2">
                    ${data.avatar ? `<img src="${data.avatar}" alt="${data.name}" class="w-12 h-12 rounded-full object-cover">` : ''}
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900">${data.name}</h3>
                        ${data.rating ? `
                            <div class="flex items-center text-sm">
                                <span class="text-yellow-500">★</span>
                                <span class="ml-1 text-gray-600">${data.rating} (${data.reviews_count || 0})</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
                ${data.distance ? `<p class="text-sm text-gray-600 mb-2">📍 ${data.distance} km distanță</p>` : ''}
                ${data.url ? `<a href="${data.url}" class="inline-block bg-primary-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-primary-700 transition">Vezi profil</a>` : ''}
            </div>
        `;
    }

    drawRadius(center, radiusKm) {
        if (!this.map) return;

        const circle = new google.maps.Circle({
            strokeColor: '#059669',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#059669',
            fillOpacity: 0.15,
            map: this.map,
            center: center,
            radius: radiusKm * 1000, // Convert to meters
        });
    }

    fitBounds() {
        if (!this.map || this.markers.length === 0) return;

        const bounds = new google.maps.LatLngBounds();
        this.markers.forEach(marker => {
            bounds.extend(marker.getPosition());
        });
        this.map.fitBounds(bounds);
        
        // Prevent zooming in too much for single marker
        if (this.markers.length === 1) {
            const listener = google.maps.event.addListener(this.map, 'idle', () => {
                if (this.map.getZoom() > 15) this.map.setZoom(15);
                google.maps.event.removeListener(listener);
            });
        }
    }

    clearMarkers() {
        this.markers.forEach(marker => marker.setMap(null));
        this.markers = [];
    }

    updateMarkers(markersData) {
        this.clearMarkers();
        this.addMarkers(markersData);
    }

    panTo(lat, lng, zoom = null) {
        if (!this.map) return;
        this.map.panTo({ lat, lng });
        if (zoom) this.map.setZoom(zoom);
    }

    getMapStyles() {
        // Optional: Custom map styling
        return [];
    }
}

// Global registry for map instances
window.mapInstances = window.mapInstances || {};
</script>
@endpush
@endonce

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof google !== 'undefined') {
        window.mapInstances['{{ $mapId }}'] = new GoogleMapComponent('{{ $mapId }}', @json($config ?? []));
    }
});
</script>
@endpush
