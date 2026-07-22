/**
 * Compare Craftsmen Manager
 * Allows users to select and compare multiple craftsmen side-by-side
 */

class CompareManager {
    constructor(options = {}) {
        this.maxCompare = options.maxCompare || 4;
        this.storageKey = 'compareCraftsmen';
        this.compareList = [];
        this.comparePanel = null;
        
        this.init();
    }

    init() {
        this.loadFromStorage();
        this.createComparePanel();
        this.bindCheckboxes();
        this.updateUI();
        
        console.log('Compare Manager initialized');
    }

    loadFromStorage() {
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                this.compareList = JSON.parse(stored);
            }
        } catch (e) {
            console.error('Error loading compare list:', e);
            this.compareList = [];
        }
    }

    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.compareList));
        } catch (e) {
            console.error('Error saving compare list:', e);
        }
    }

    createComparePanel() {
        // Create floating compare panel
        this.comparePanel = document.createElement('div');
        this.comparePanel.id = 'compare-panel';
        this.comparePanel.className = 'compare-panel hidden';
        this.comparePanel.innerHTML = `
            <div class="compare-panel-content">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">
                        Comparație (<span id="compare-count">0</span>/${this.maxCompare})
                    </h4>
                    <button type="button" class="text-gray-400 hover:text-gray-600" id="close-compare-panel">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div id="compare-items" class="flex gap-2 mb-3 overflow-x-auto pb-2"></div>
                <div class="flex gap-2">
                    <button type="button" 
                            id="compare-btn"
                            class="flex-1 bg-primary-600 hover:bg-primary-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed"
                            disabled>
                        Compară
                    </button>
                    <button type="button" 
                            id="clear-compare-btn"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                        Șterge tot
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(this.comparePanel);

        // Bind panel buttons
        document.getElementById('close-compare-panel')?.addEventListener('click', () => {
            this.hidePanel();
        });

        document.getElementById('compare-btn')?.addEventListener('click', () => {
            this.openComparePage();
        });

        document.getElementById('clear-compare-btn')?.addEventListener('click', () => {
            this.clearAll();
        });
    }

    bindCheckboxes() {
        document.querySelectorAll('.compare-select').forEach(checkbox => {
            // Remove existing listeners
            checkbox.replaceWith(checkbox.cloneNode(true));
        });

        document.querySelectorAll('.compare-select').forEach(checkbox => {
            const craftsmanId = checkbox.dataset.craftsmanId;
            
            // Set initial state
            checkbox.checked = this.compareList.some(c => c.id == craftsmanId);
            this.updateCheckboxVisual(checkbox);

            checkbox.addEventListener('change', (e) => {
                const id = e.target.dataset.craftsmanId;
                const name = e.target.dataset.craftsmanName;
                
                if (e.target.checked) {
                    this.add({ id, name });
                } else {
                    this.remove(id);
                }
                this.updateCheckboxVisual(e.target);
            });
        });
    }

    updateCheckboxVisual(checkbox) {
        const wrapper = checkbox.closest('label');
        const span = wrapper?.querySelector('span');
        const svg = wrapper?.querySelector('svg');
        
        if (span && svg) {
            if (checkbox.checked) {
                span.classList.add('bg-primary-600', 'border-primary-600');
                span.classList.remove('border-gray-300', 'bg-white/90');
                svg.classList.remove('opacity-0');
                svg.classList.add('opacity-100');
            } else {
                span.classList.remove('bg-primary-600', 'border-primary-600');
                span.classList.add('border-gray-300', 'bg-white/90');
                svg.classList.add('opacity-0');
                svg.classList.remove('opacity-100');
            }
        }
    }

    add(craftsman) {
        if (this.compareList.length >= this.maxCompare) {
            this.showNotification(`Poți compara maximum ${this.maxCompare} meseriași`, 'warning');
            
            // Uncheck the checkbox
            const checkbox = document.querySelector(`.compare-select[data-craftsman-id="${craftsman.id}"]`);
            if (checkbox) {
                checkbox.checked = false;
                this.updateCheckboxVisual(checkbox);
            }
            return false;
        }

        if (!this.compareList.some(c => c.id == craftsman.id)) {
            this.compareList.push(craftsman);
            this.saveToStorage();
            this.updateUI();
            this.showPanel();
        }
        return true;
    }

    remove(craftsmanId) {
        this.compareList = this.compareList.filter(c => c.id != craftsmanId);
        this.saveToStorage();
        this.updateUI();

        // Update checkbox
        const checkbox = document.querySelector(`.compare-select[data-craftsman-id="${craftsmanId}"]`);
        if (checkbox) {
            checkbox.checked = false;
            this.updateCheckboxVisual(checkbox);
        }

        if (this.compareList.length === 0) {
            this.hidePanel();
        }
    }

    clearAll() {
        this.compareList = [];
        this.saveToStorage();
        this.updateUI();
        this.hidePanel();

        // Uncheck all checkboxes
        document.querySelectorAll('.compare-select').forEach(checkbox => {
            checkbox.checked = false;
            this.updateCheckboxVisual(checkbox);
        });
    }

    updateUI() {
        // Update count
        const countEl = document.getElementById('compare-count');
        if (countEl) {
            countEl.textContent = this.compareList.length;
        }

        // Update items list
        const itemsContainer = document.getElementById('compare-items');
        if (itemsContainer) {
            itemsContainer.innerHTML = this.compareList.map(craftsman => `
                <div class="compare-item flex items-center gap-2 bg-gray-100 rounded-lg px-3 py-2 flex-shrink-0">
                    <span class="text-sm font-medium text-gray-700 truncate max-w-24">${craftsman.name}</span>
                    <button type="button" 
                            class="text-gray-400 hover:text-red-500 transition"
                            onclick="compareManager.remove('${craftsman.id}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `).join('');
        }

        // Update compare button state
        const compareBtn = document.getElementById('compare-btn');
        if (compareBtn) {
            compareBtn.disabled = this.compareList.length < 2;
        }

        // Update all checkboxes
        document.querySelectorAll('.compare-select').forEach(checkbox => {
            const isSelected = this.compareList.some(c => c.id == checkbox.dataset.id);
            checkbox.checked = isSelected;
            this.updateCheckboxVisual(checkbox);
        });
    }

    showPanel() {
        this.comparePanel?.classList.remove('hidden');
        this.comparePanel?.classList.add('slide-in-up');
    }

    hidePanel() {
        this.comparePanel?.classList.add('hidden');
        this.comparePanel?.classList.remove('slide-in-up');
    }

    openComparePage() {
        if (this.compareList.length < 2) {
            this.showNotification('Selectează cel puțin 2 meseriași pentru comparație', 'warning');
            return;
        }

        const ids = this.compareList.map(c => c.id).join(',');
        window.location.href = `/meseriasi/compara?ids=${ids}`;
    }

    showNotification(message, type = 'info') {
        // Create notification
        const notification = document.createElement('div');
        notification.className = `fixed bottom-24 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all transform translate-y-2 opacity-0 ${
            type === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'
        }`;
        notification.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium">${message}</span>
            </div>
        `;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-y-2', 'opacity-0');
        }, 10);

        // Remove after delay
        setTimeout(() => {
            notification.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    getCompareList() {
        return this.compareList;
    }

    getCompareIds() {
        return this.compareList.map(c => c.id);
    }
}

// Compare panel styles
const compareStyles = document.createElement('style');
compareStyles.textContent = `
    .compare-panel {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 40;
        width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        border: 1px solid #e5e7eb;
    }
    
    .compare-panel.hidden {
        display: none;
    }
    
    .compare-panel-content {
        padding: 16px;
    }
    
    .compare-item {
        min-width: fit-content;
    }
    
    .compare-checkbox {
        position: relative;
    }
    
    .compare-checkbox input:checked + .compare-icon {
        color: #065F46 !important;
    }
    
    .slide-in-up {
        animation: slideInUp 0.3s ease-out;
    }
    
    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @media (max-width: 640px) {
        .compare-panel {
            width: calc(100% - 40px);
            left: 20px;
            right: 20px;
        }
    }
`;
document.head.appendChild(compareStyles);

// Initialize
let compareManager;
document.addEventListener('DOMContentLoaded', () => {
    compareManager = new CompareManager({ maxCompare: 4 });
    window.compareManager = compareManager;
});

export default CompareManager;
