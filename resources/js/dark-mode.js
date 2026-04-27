/**
 * Dark Mode Manager
 * Handles dark mode toggle with localStorage persistence
 */
class DarkModeManager {
    constructor() {
        this.STORAGE_KEY = 'fixacasa_dark_mode';
        this.init();
    }

    init() {
        // Apply saved preference or system preference on load
        this.applyInitialTheme();
        
        // Listen for system preference changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!this.hasUserPreference()) {
                this.setDarkMode(e.matches, false);
            }
        });

        // Setup toggle buttons when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupToggleButtons());
        } else {
            this.setupToggleButtons();
        }
    }

    applyInitialTheme() {
        const savedPreference = localStorage.getItem(this.STORAGE_KEY);
        
        if (savedPreference !== null) {
            this.setDarkMode(savedPreference === 'true', false);
        } else {
            // Use system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            this.setDarkMode(prefersDark, false);
        }
    }

    hasUserPreference() {
        return localStorage.getItem(this.STORAGE_KEY) !== null;
    }

    isDarkMode() {
        return document.documentElement.classList.contains('dark');
    }

    setDarkMode(enabled, savePreference = true) {
        if (enabled) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        if (savePreference) {
            localStorage.setItem(this.STORAGE_KEY, enabled.toString());
        }

        this.updateToggleButtons();
        
        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('darkModeChanged', { detail: { darkMode: enabled } }));
    }

    toggle() {
        this.setDarkMode(!this.isDarkMode());
    }

    setupToggleButtons() {
        // Find all dark mode toggle buttons
        document.querySelectorAll('[data-dark-mode-toggle]').forEach(button => {
            button.addEventListener('click', () => this.toggle());
        });

        this.updateToggleButtons();
    }

    updateToggleButtons() {
        const isDark = this.isDarkMode();
        
        document.querySelectorAll('[data-dark-mode-toggle]').forEach(button => {
            const sunIcon = button.querySelector('.sun-icon');
            const moonIcon = button.querySelector('.moon-icon');
            
            if (sunIcon && moonIcon) {
                sunIcon.classList.toggle('hidden', isDark);
                moonIcon.classList.toggle('hidden', !isDark);
            }
            
            button.setAttribute('aria-pressed', isDark.toString());
        });
    }
}

// Initialize dark mode manager
window.darkModeManager = new DarkModeManager();

// Export for module usage
export default DarkModeManager;
