/**
 * Touch Gestures Manager
 * Implements swipe gestures, pull-to-refresh, and touch-friendly interactions
 */

class TouchGesturesManager {
    constructor() {
        this.touchStartX = 0;
        this.touchStartY = 0;
        this.touchEndX = 0;
        this.touchEndY = 0;
        this.pullStartY = 0;
        this.isPulling = false;
        this.pullThreshold = 80;
        this.swipeThreshold = 50;
        this.isRefreshing = false;
        
        this.init();
    }

    init() {
        this.setupPullToRefresh();
        this.setupSwipeNavigation();
        this.setupSwipeableCards();
        this.setupDoubleTapZoom();
        this.setupLongPress();
        this.setupMobileMenuGesture();
        this.setupImageGallerySwipe();
        this.setupHapticFeedback();
        
        console.log('[Touch] Gestures Manager initialized');
    }

    /**
     * Pull to Refresh
     * Pull down on the page to refresh content
     */
    setupPullToRefresh() {
        // Create pull indicator if it doesn't exist
        if (!document.getElementById('pull-refresh-indicator')) {
            const indicator = document.createElement('div');
            indicator.id = 'pull-refresh-indicator';
            indicator.className = 'pull-to-refresh';
            indicator.innerHTML = `
                <svg class="w-6 h-6 text-primary-600 pull-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
                <svg class="w-6 h-6 text-primary-600 pull-spinner hidden" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            document.body.appendChild(indicator);
        }

        const indicator = document.getElementById('pull-refresh-indicator');
        const arrow = indicator.querySelector('.pull-arrow');
        const spinner = indicator.querySelector('.pull-spinner');
        let startY = 0;
        let currentY = 0;

        document.addEventListener('touchstart', (e) => {
            if (window.scrollY === 0 && !this.isRefreshing) {
                startY = e.touches[0].clientY;
                this.isPulling = true;
            }
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (!this.isPulling || this.isRefreshing) return;
            
            currentY = e.touches[0].clientY;
            const pullDistance = currentY - startY;
            
            if (pullDistance > 0 && window.scrollY === 0) {
                const progress = Math.min(pullDistance / this.pullThreshold, 1);
                const translateY = Math.min(pullDistance * 0.5, this.pullThreshold);
                
                indicator.style.top = `${translateY - 50}px`;
                indicator.classList.add('visible');
                
                // Rotate arrow based on progress
                arrow.style.transform = `rotate(${progress * 180}deg)`;
                
                if (progress >= 1) {
                    indicator.classList.add('ready');
                } else {
                    indicator.classList.remove('ready');
                }
            }
        }, { passive: true });

        document.addEventListener('touchend', () => {
            if (!this.isPulling || this.isRefreshing) return;
            
            const pullDistance = currentY - startY;
            
            if (pullDistance > this.pullThreshold && window.scrollY === 0) {
                this.triggerRefresh(indicator, arrow, spinner);
            } else {
                this.resetPullIndicator(indicator, arrow);
            }
            
            this.isPulling = false;
        });
    }

    triggerRefresh(indicator, arrow, spinner) {
        this.isRefreshing = true;
        this.triggerHaptic('medium');
        
        arrow.classList.add('hidden');
        spinner.classList.remove('hidden');
        indicator.classList.add('refreshing');
        
        // Dispatch custom event for pages to listen to
        window.dispatchEvent(new CustomEvent('pullToRefresh'));
        
        // Refresh the page after a short delay
        setTimeout(() => {
            window.location.reload();
        }, 500);
    }

    resetPullIndicator(indicator, arrow) {
        indicator.style.top = '-60px';
        indicator.classList.remove('visible', 'ready');
        arrow.style.transform = 'rotate(0deg)';
    }

    /**
     * Swipe Navigation
     * Swipe left/right to navigate between pages
     */
    setupSwipeNavigation() {
        let startX = 0;
        let startY = 0;
        let isHorizontalSwipe = null;

        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isHorizontalSwipe = null;
        }, { passive: true });

        document.addEventListener('touchmove', (e) => {
            if (isHorizontalSwipe === null) {
                const diffX = Math.abs(e.touches[0].clientX - startX);
                const diffY = Math.abs(e.touches[0].clientY - startY);
                isHorizontalSwipe = diffX > diffY;
            }
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (!isHorizontalSwipe) return;
            
            const endX = e.changedTouches[0].clientX;
            const diffX = endX - startX;
            
            // Only trigger on edges
            if (startX < 30 && diffX > this.swipeThreshold * 2) {
                // Swipe right from left edge - go back
                this.triggerHaptic('light');
                if (window.history.length > 1) {
                    window.history.back();
                }
            }
        });
    }

    /**
     * Swipeable Cards
     * Swipe cards to reveal actions
     */
    setupSwipeableCards() {
        document.querySelectorAll('.swipeable-card').forEach(card => {
            this.makeSwipeable(card);
        });

        // Watch for dynamically added cards
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) {
                        const cards = node.classList?.contains('swipeable-card') 
                            ? [node] 
                            : node.querySelectorAll?.('.swipeable-card') || [];
                        cards.forEach(card => this.makeSwipeable(card));
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    makeSwipeable(card) {
        if (card.dataset.swipeEnabled) return;
        card.dataset.swipeEnabled = 'true';

        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        card.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            isDragging = true;
            card.style.transition = 'none';
        }, { passive: true });

        card.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            currentX = e.touches[0].clientX;
            const diff = currentX - startX;
            
            // Only allow left swipe
            if (diff < 0) {
                const translateX = Math.max(diff, -100);
                card.style.transform = `translateX(${translateX}px)`;
            }
        }, { passive: true });

        card.addEventListener('touchend', () => {
            isDragging = false;
            card.style.transition = 'transform 0.3s ease';
            
            const diff = currentX - startX;
            if (diff < -50) {
                // Show actions
                card.style.transform = 'translateX(-80px)';
                card.classList.add('swiped');
                this.triggerHaptic('light');
            } else {
                // Reset
                card.style.transform = 'translateX(0)';
                card.classList.remove('swiped');
            }
        });

        // Reset on tap elsewhere
        document.addEventListener('touchstart', (e) => {
            if (!card.contains(e.target) && card.classList.contains('swiped')) {
                card.style.transform = 'translateX(0)';
                card.classList.remove('swiped');
            }
        });
    }

    /**
     * Double Tap to Zoom
     * Double tap on images to zoom in/out
     */
    setupDoubleTapZoom() {
        let lastTap = 0;
        
        document.addEventListener('touchend', (e) => {
            const target = e.target.closest('.zoomable, .gallery-image');
            if (!target) return;
            
            const currentTime = new Date().getTime();
            const tapLength = currentTime - lastTap;
            
            if (tapLength < 300 && tapLength > 0) {
                e.preventDefault();
                this.toggleZoom(target);
                this.triggerHaptic('light');
            }
            
            lastTap = currentTime;
        });
    }

    toggleZoom(element) {
        if (element.classList.contains('zoomed')) {
            element.classList.remove('zoomed');
            element.style.transform = '';
            element.style.position = '';
            element.style.zIndex = '';
        } else {
            element.classList.add('zoomed');
            element.style.transform = 'scale(2)';
            element.style.position = 'relative';
            element.style.zIndex = '100';
        }
    }

    /**
     * Long Press
     * Long press to show context menu or additional options
     */
    setupLongPress() {
        let pressTimer = null;
        let isLongPress = false;

        document.addEventListener('touchstart', (e) => {
            const target = e.target.closest('.long-pressable, .craftsman-card');
            if (!target) return;

            isLongPress = false;
            pressTimer = setTimeout(() => {
                isLongPress = true;
                this.triggerHaptic('medium');
                this.showContextMenu(target, e.touches[0]);
            }, 500);
        }, { passive: true });

        document.addEventListener('touchend', () => {
            clearTimeout(pressTimer);
        });

        document.addEventListener('touchmove', () => {
            clearTimeout(pressTimer);
        });
    }

    showContextMenu(target, touch) {
        // Remove existing context menu
        const existingMenu = document.getElementById('touch-context-menu');
        if (existingMenu) existingMenu.remove();

        const menu = document.createElement('div');
        menu.id = 'touch-context-menu';
        menu.className = 'fixed bg-white dark:bg-gray-800 rounded-xl shadow-2xl border dark:border-gray-700 z-[9999] py-2 min-w-48 animate-scale-in';
        
        // Get craftsman data if available
        const craftsmanId = target.dataset?.id || target.dataset?.craftsmanId;
        const craftsmanSlug = target.dataset?.slug;
        
        if (craftsmanId) {
            menu.innerHTML = `
                <button class="context-menu-item" data-action="view">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Vezi profil</span>
                </button>
                <button class="context-menu-item" data-action="compare">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Adaugă la comparație</span>
                </button>
                <button class="context-menu-item" data-action="favorite">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span>Salvează în favorite</span>
                </button>
                <button class="context-menu-item" data-action="share">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    <span>Distribuie</span>
                </button>
            `;
        } else {
            menu.innerHTML = `
                <button class="context-menu-item" data-action="copy">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                    <span>Copiază</span>
                </button>
                <button class="context-menu-item" data-action="share">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    <span>Distribuie</span>
                </button>
            `;
        }

        // Position menu
        const x = Math.min(touch.clientX, window.innerWidth - 200);
        const y = Math.min(touch.clientY, window.innerHeight - 200);
        menu.style.left = `${x}px`;
        menu.style.top = `${y}px`;

        document.body.appendChild(menu);

        // Handle menu actions
        menu.querySelectorAll('.context-menu-item').forEach(item => {
            item.addEventListener('click', () => {
                const action = item.dataset.action;
                this.handleContextAction(action, target);
                menu.remove();
            });
        });

        // Close on tap outside
        setTimeout(() => {
            document.addEventListener('touchstart', function closeMenu(e) {
                if (!menu.contains(e.target)) {
                    menu.remove();
                    document.removeEventListener('touchstart', closeMenu);
                }
            });
        }, 100);
    }

    handleContextAction(action, target) {
        const craftsmanId = target.dataset?.id || target.dataset?.craftsmanId;
        const craftsmanSlug = target.dataset?.slug;

        switch (action) {
            case 'view':
                if (craftsmanSlug) {
                    window.location.href = `/meserias/${craftsmanSlug}`;
                }
                break;
            case 'compare':
                if (window.compareManager && craftsmanId) {
                    const name = target.dataset?.name || target.querySelector('h3')?.textContent || 'Meseriaș';
                    window.compareManager.add({ id: craftsmanId, name });
                }
                break;
            case 'favorite':
                if (craftsmanId) {
                    this.toggleFavorite(craftsmanId);
                }
                break;
            case 'share':
                this.shareContent(target);
                break;
            case 'copy':
                this.copyToClipboard(window.location.href);
                break;
        }
    }

    toggleFavorite(craftsmanId) {
        fetch('/favorite/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
            },
            body: JSON.stringify({ craftsman_id: craftsmanId })
        })
        .then(response => response.json())
        .then(data => {
            this.showToast(data.is_favorite ? 'Adăugat la favorite!' : 'Eliminat din favorite');
        })
        .catch(error => {
            console.error('Error toggling favorite:', error);
        });
    }

    shareContent(target) {
        const title = target.querySelector('h3')?.textContent || document.title;
        const url = target.dataset?.slug ? `/meserias/${target.dataset.slug}` : window.location.href;

        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            }).catch(() => {});
        } else {
            this.copyToClipboard(window.location.origin + url);
            this.showToast('Link copiat!');
        }
    }

    copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            this.showToast('Copiat în clipboard!');
        }).catch(() => {
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            this.showToast('Copiat!');
        });
    }

    /**
     * Mobile Menu Gesture
     * Swipe from edge to open mobile menu
     */
    setupMobileMenuGesture() {
        let startX = 0;
        let menuOpen = false;

        // Create mobile menu overlay if needed
        const existingOverlay = document.getElementById('mobile-menu-overlay');
        if (!existingOverlay) {
            this.createMobileMenu();
        }

        document.addEventListener('touchstart', (e) => {
            if (e.touches[0].clientX < 20) {
                startX = e.touches[0].clientX;
            }
        }, { passive: true });

        document.addEventListener('touchend', (e) => {
            if (startX > 0 && startX < 20) {
                const endX = e.changedTouches[0].clientX;
                if (endX - startX > 100) {
                    this.openMobileMenu();
                    this.triggerHaptic('light');
                }
            }
            startX = 0;
        });
    }

    createMobileMenu() {
        const overlay = document.createElement('div');
        overlay.id = 'mobile-menu-overlay';
        overlay.className = 'mobile-menu-overlay';
        overlay.innerHTML = `
            <div class="mobile-menu-panel">
                <div class="p-4 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-xl font-bold text-primary-600">Omul Potrivit</span>
                        <button id="close-mobile-menu" class="p-2 text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <nav class="p-4 space-y-1">
                    <a href="/" class="mobile-nav-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Acasă</span>
                    </a>
                    <a href="/#categories" class="mobile-nav-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Categorii</span>
                    </a>
                    <a href="/articole" class="mobile-nav-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                        <span>Articole</span>
                    </a>
                    <a href="/intrebari" class="mobile-nav-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Întrebări</span>
                    </a>
                    <a href="/despre" class="mobile-nav-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Despre noi</span>
                    </a>
                    <a href="/contact" class="mobile-nav-item">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Contact</span>
                    </a>
                </nav>
                <div class="absolute bottom-0 left-0 right-0 p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <div class="flex gap-2">
                        <a href="/login" class="flex-1 text-center py-2 px-4 bg-primary-600 text-white rounded-lg font-medium">
                            Intră în cont
                        </a>
                        <a href="/register/client" class="flex-1 text-center py-2 px-4 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium">
                            Creează cont
                        </a>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);

        // Close handlers
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) {
                this.closeMobileMenu();
            }
        });

        document.getElementById('close-mobile-menu')?.addEventListener('click', () => {
            this.closeMobileMenu();
        });
    }

    openMobileMenu() {
        const overlay = document.getElementById('mobile-menu-overlay');
        if (overlay) {
            overlay.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    }

    closeMobileMenu() {
        const overlay = document.getElementById('mobile-menu-overlay');
        if (overlay) {
            overlay.classList.remove('open');
            document.body.style.overflow = '';
        }
    }

    /**
     * Image Gallery Swipe
     * Swipe through images in galleries
     */
    setupImageGallerySwipe() {
        document.querySelectorAll('.gallery-container, .image-carousel').forEach(gallery => {
            this.makeGallerySwipeable(gallery);
        });
    }

    makeGallerySwipeable(gallery) {
        let startX = 0;
        let currentIndex = 0;
        const items = gallery.querySelectorAll('.gallery-item, .carousel-item');
        if (items.length === 0) return;

        gallery.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });

        gallery.addEventListener('touchend', (e) => {
            const endX = e.changedTouches[0].clientX;
            const diff = endX - startX;

            if (Math.abs(diff) > this.swipeThreshold) {
                if (diff > 0 && currentIndex > 0) {
                    currentIndex--;
                    this.triggerHaptic('light');
                } else if (diff < 0 && currentIndex < items.length - 1) {
                    currentIndex++;
                    this.triggerHaptic('light');
                }

                items.forEach((item, index) => {
                    item.style.transform = `translateX(${(index - currentIndex) * 100}%)`;
                });
            }
        });
    }

    /**
     * Haptic Feedback
     * Provide tactile feedback on interactions
     */
    setupHapticFeedback() {
        // Add haptic feedback to buttons
        document.querySelectorAll('button, .btn, [role="button"]').forEach(btn => {
            btn.addEventListener('touchstart', () => {
                this.triggerHaptic('light');
            }, { passive: true });
        });
    }

    triggerHaptic(intensity = 'light') {
        if ('vibrate' in navigator) {
            switch (intensity) {
                case 'light':
                    navigator.vibrate(10);
                    break;
                case 'medium':
                    navigator.vibrate(25);
                    break;
                case 'heavy':
                    navigator.vibrate(50);
                    break;
            }
        }
    }

    /**
     * Toast Notifications
     */
    showToast(message, duration = 2000) {
        const existingToast = document.querySelector('.touch-toast');
        if (existingToast) existingToast.remove();

        const toast = document.createElement('div');
        toast.className = 'touch-toast';
        toast.textContent = message;
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.add('visible');
        });

        setTimeout(() => {
            toast.classList.remove('visible');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize on touch devices
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        window.touchGestures = new TouchGesturesManager();
    }
});

export default TouchGesturesManager;
