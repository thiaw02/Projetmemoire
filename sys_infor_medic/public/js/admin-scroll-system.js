/**
 * SYSTÈME DE DÉFILEMENT PROFESSIONNEL ADMIN
 * Gestion intelligente de la sidebar et optimisations UX
 */

class AdminScrollSystem {
    constructor() {
        this.lastScrollTop = 0;
        this.scrollDelta = 5;
        this.scrollDirection = 'down';
        this.isScrolling = false;
        this.scrollTimer = null;
        
        this.sidebar = document.querySelector('.admin-intelligent-sidebar');
        this.progressBar = null;
        this.scrollToTopBtn = null;
        this.navbar = document.querySelector('.navbar');
        
        this.init();
    }
    
    init() {
        this.createScrollElements();
        this.bindEvents();
        this.setupIntersectionObserver();
        this.setupPerformanceOptimizations();
        
        console.log('Admin Scroll System initialisé');
    }
    
    /**
     * Création des éléments de défilement
     */
    createScrollElements() {
        // Barre de progression
        if (!document.querySelector('.scroll-progress-bar')) {
            this.progressBar = document.createElement('div');
            this.progressBar.className = 'scroll-progress-bar';
            document.body.appendChild(this.progressBar);
        } else {
            this.progressBar = document.querySelector('.scroll-progress-bar');
        }
        
        // Bouton retour en haut
        if (!document.querySelector('.scroll-to-top')) {
            this.scrollToTopBtn = document.createElement('button');
            this.scrollToTopBtn.className = 'scroll-to-top';
            this.scrollToTopBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            this.scrollToTopBtn.setAttribute('aria-label', 'Retour en haut');
            this.scrollToTopBtn.title = 'Retour en haut';
            document.body.appendChild(this.scrollToTopBtn);
        } else {
            this.scrollToTopBtn = document.querySelector('.scroll-to-top');
        }
    }
    
    /**
     * Liaison des événements
     */
    bindEvents() {
        // Événement de défilement principal avec throttle
        window.addEventListener('scroll', this.throttle(this.handleScroll.bind(this), 16));
        
        // Bouton retour en haut
        if (this.scrollToTopBtn) {
            this.scrollToTopBtn.addEventListener('click', this.scrollToTop.bind(this));
        }
        
        // Gestion du redimensionnement
        window.addEventListener('resize', this.throttle(this.handleResize.bind(this), 250));
        
        // Gestion des touches clavier
        document.addEventListener('keydown', this.handleKeyboard.bind(this));
        
        // Optimisation pour les appareils tactiles
        if (this.isTouchDevice()) {
            this.setupTouchOptimizations();
        }
    }
    
    /**
     * Gestion principal du défilement
     */
    handleScroll() {
        const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        
        // Détection de la direction de défilement
        if (Math.abs(this.lastScrollTop - currentScrollTop) > this.scrollDelta) {
            this.scrollDirection = currentScrollTop > this.lastScrollTop ? 'down' : 'up';
        }
        
        // Mise à jour de la barre de progression
        this.updateProgressBar(currentScrollTop, documentHeight, windowHeight);
        
        // Gestion de la sidebar
        this.updateSidebar(currentScrollTop);
        
        // Gestion du bouton retour en haut
        this.updateScrollToTopBtn(currentScrollTop);
        
        // Gestion de la navbar
        this.updateNavbar(currentScrollTop);
        
        // Animations d'éléments au défilement
        this.triggerScrollAnimations();
        
        this.lastScrollTop = currentScrollTop;
        
        // Marquer comme défilant
        this.isScrolling = true;
        clearTimeout(this.scrollTimer);
        this.scrollTimer = setTimeout(() => {
            this.isScrolling = false;
        }, 150);
    }
    
    /**
     * Mise à jour de la barre de progression
     */
    updateProgressBar(scrollTop, documentHeight, windowHeight) {
        if (!this.progressBar) return;
        
        const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;
        this.progressBar.style.width = Math.min(scrollPercent, 100) + '%';
    }
    
    /**
     * Gestion de la sidebar - SIDEBAR FIXE SANS DÉPLACEMENT
     */
    updateSidebar(scrollTop) {
        if (!this.sidebar || window.innerWidth <= 991) return;
        
        // Sidebar reste fixe - pas de déplacement au scroll
        // On s'assure juste qu'elle reste visible
        this.sidebar.classList.add('sidebar-visible');
        this.sidebar.classList.remove('sidebar-compact', 'sidebar-hidden', 'scroll-up', 'scroll-down');
    }
    
    /**
     * Gestion du bouton retour en haut
     */
    updateScrollToTopBtn(scrollTop) {
        if (!this.scrollToTopBtn) return;
        
        if (scrollTop > 300) {
            this.scrollToTopBtn.classList.add('visible');
        } else {
            this.scrollToTopBtn.classList.remove('visible');
        }
    }
    
    /**
     * Gestion de la navbar au défilement
     */
    updateNavbar(scrollTop) {
        if (!this.navbar) return;
        
        if (scrollTop > 50) {
            this.navbar.classList.add('scrolled');
        } else {
            this.navbar.classList.remove('scrolled');
        }
    }
    
    /**
     * Animation des éléments au défilement
     */
    triggerScrollAnimations() {
        const elements = document.querySelectorAll('.scroll-fade-in, .scroll-slide-left, .scroll-scale-in');
        
        elements.forEach(element => {
            if (this.isElementInViewport(element)) {
                element.classList.add('visible');
            }
        });
    }
    
    /**
     * Défilement fluide vers le haut
     */
    scrollToTop() {
        const startScrollTop = window.pageYOffset;
        const duration = 600;
        const startTime = performance.now();
        
        const animateScroll = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Fonction d'easing
            const easeOutCubic = 1 - Math.pow(1 - progress, 3);
            
            window.scrollTo(0, startScrollTop * (1 - easeOutCubic));
            
            if (progress < 1) {
                requestAnimationFrame(animateScroll);
            }
        };
        
        requestAnimationFrame(animateScroll);
    }
    
    /**
     * Configuration de l'Intersection Observer pour les animations
     */
    setupIntersectionObserver() {
        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, options);
        
        // Observer les éléments avec animations
        const animatedElements = document.querySelectorAll('.scroll-fade-in, .scroll-slide-left, .scroll-scale-in');
        animatedElements.forEach(element => observer.observe(element));
    }
    
    /**
     * Gestion du redimensionnement
     */
    handleResize() {
        // Recalculer les dimensions
        if (window.innerWidth <= 991 && this.sidebar) {
            this.sidebar.classList.remove('sidebar-compact', 'sidebar-hidden');
            this.sidebar.classList.add('sidebar-visible');
        }
    }
    
    /**
     * Gestion des raccourcis clavier
     */
    handleKeyboard(event) {
        // Retour en haut avec la touche Home
        if (event.key === 'Home' && !event.target.matches('input, textarea')) {
            event.preventDefault();
            this.scrollToTop();
        }
        
        // Aller en bas avec la touche End
        if (event.key === 'End' && !event.target.matches('input, textarea')) {
            event.preventDefault();
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: 'smooth'
            });
        }
    }
    
    /**
     * Optimisations pour appareils tactiles
     */
    setupTouchOptimizations() {
        let touchStartY = 0;
        let touchEndY = 0;
        
        document.addEventListener('touchstart', (e) => {
            touchStartY = e.changedTouches[0].screenY;
        });
        
        document.addEventListener('touchend', (e) => {
            touchEndY = e.changedTouches[0].screenY;
            const swipeDistance = touchStartY - touchEndY;
            
            // Swipe vers le haut pour retour en haut (sur sidebar)
            if (this.sidebar && this.sidebar.contains(e.target) && swipeDistance > 100) {
                this.scrollToTop();
            }
        });
    }
    
    /**
     * Optimisations de performance
     */
    setupPerformanceOptimizations() {
        // Utiliser la GPU pour les animations
        if (this.sidebar) {
            this.sidebar.classList.add('gpu-accelerated');
        }
        
        // Optimisation des cartes
        const cards = document.querySelectorAll('.card, .kpi-card');
        cards.forEach(card => {
            card.classList.add('admin-card-optimized', 'gpu-accelerated');
        });
        
        // Lazy loading pour les images
        this.setupLazyLoading();
    }
    
    /**
     * Configuration du lazy loading
     */
    setupLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.add('fade-in');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            images.forEach(img => imageObserver.observe(img));
        }
    }
    
    /**
     * Utilitaires
     */
    
    throttle(func, delay) {
        let timeoutId;
        let lastExecTime = 0;
        return function (...args) {
            const currentTime = Date.now();
            
            if (currentTime - lastExecTime > delay) {
                func.apply(this, args);
                lastExecTime = currentTime;
            } else {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(() => {
                    func.apply(this, args);
                    lastExecTime = Date.now();
                }, delay - (currentTime - lastExecTime));
            }
        };
    }
    
    isElementInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
    
    isTouchDevice() {
        return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    }
    
    /**
     * API publique
     */
    
    // Toggle sidebar mini mode - DÉSACTIVÉ pour sidebar fixe
    toggleSidebarMini() {
        // Fonctionnalité désactivée - sidebar reste fixe
        return;
    }
    
    // Forcer la mise à jour
    refresh() {
        this.handleScroll();
    }
    
    // Détruire l'instance
    destroy() {
        window.removeEventListener('scroll', this.handleScroll);
        window.removeEventListener('resize', this.handleResize);
        document.removeEventListener('keydown', this.handleKeyboard);
        
        if (this.progressBar) {
            this.progressBar.remove();
        }
        
        if (this.scrollToTopBtn) {
            this.scrollToTopBtn.remove();
        }
        
        console.log('Admin Scroll System détruit');
    }
}

/**
 * Initialisation automatique
 */
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier si nous sommes sur une page admin
    const isAdminPage = document.body.classList.contains('admin-page') || 
                        window.location.pathname.includes('/admin/') ||
                        document.querySelector('.admin-intelligent-sidebar') !== null;
    
    if (isAdminPage) {
        // Ajouter les classes nécessaires
        document.body.classList.add('page-transition');
        
        // Initialiser le système de défilement
        window.adminScrollSystem = new AdminScrollSystem();
        
        // Animation d'entrée de page
        setTimeout(() => {
            const elements = document.querySelectorAll('.scroll-fade-in, .scroll-slide-left, .scroll-scale-in');
            elements.forEach((element, index) => {
                setTimeout(() => {
                    element.classList.add('visible');
                }, index * 100);
            });
        }, 200);
        
        // Gestion des toasts modernes
        window.showModernToast = function(message, type = 'success', duration = 5000) {
            const toast = document.createElement('div');
            toast.className = `modern-toast ${type}`;
            toast.innerHTML = `
                <div class="toast-header">
                    <div class="toast-icon ${type}">
                        <i class="bi bi-${type === 'success' ? 'check' : type === 'error' ? 'x' : type === 'warning' ? 'exclamation' : 'info'}"></i>
                    </div>
                    <h6 class="toast-title">${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                    <button class="toast-close" onclick="this.closest('.modern-toast').remove()">×</button>
                </div>
                <div class="toast-body">${message}</div>
                <div class="toast-progress">
                    <div class="toast-progress-bar"></div>
                </div>
            `;
            
            let container = document.querySelector('.toast-container');
            if (!container) {
                container = document.createElement('div');
                container.className = 'toast-container';
                document.body.appendChild(container);
            }
            
            container.appendChild(toast);
            
            // Animation d'apparition
            setTimeout(() => toast.classList.add('show'), 100);
            
            // Animation de la barre de progression
            const progressBar = toast.querySelector('.toast-progress-bar');
            setTimeout(() => {
                progressBar.style.transform = 'translateX(-100%)';
            }, 100);
            
            // Suppression automatique
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        };
        
        console.log('Système de défilement admin initialisé');
    }
});

// Export pour utilisation modulaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdminScrollSystem;
}