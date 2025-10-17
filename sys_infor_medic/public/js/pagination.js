/**
 * Amélioration de l'expérience de pagination
 * Scroll fluide vers le haut après changement de page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Défilement fluide vers le haut après changement de page
    const paginationLinks = document.querySelectorAll('.pagination a, .btn[href*="page="]');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            // Ajouter un indicateur de chargement
            const loadingSpinner = document.createElement('div');
            loadingSpinner.className = 'position-fixed top-50 start-50 translate-middle';
            loadingSpinner.style.zIndex = '9999';
            loadingSpinner.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            `;
            
            // Afficher le spinner temporairement
            document.body.appendChild(loadingSpinner);
            
            // Supprimer le spinner après un délai court
            setTimeout(() => {
                if (document.body.contains(loadingSpinner)) {
                    document.body.removeChild(loadingSpinner);
                }
            }, 1000);
        });
    });
    
    // Scroll fluide vers le début du tableau après chargement d'une nouvelle page
    if (window.location.search.includes('page=')) {
        const tableContainer = document.querySelector('.table-responsive, .card');
        if (tableContainer) {
            setTimeout(() => {
                tableContainer.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }, 100);
        }
    }
    
    // Amélioration de l'affichage de pagination sur mobile
    const paginationContainers = document.querySelectorAll('nav[role="navigation"]');
    
    paginationContainers.forEach(container => {
        const pagination = container.querySelector('.pagination');
        if (pagination) {
            // Ajouter une classe pour les animations
            pagination.classList.add('pagination-animated');
            
            // Mettre en évidence la page active
            const activePage = pagination.querySelector('.page-item.active');
            if (activePage) {
                activePage.scrollIntoView({ block: 'nearest', inline: 'center' });
            }
        }
    });
    
    // Raccourcis clavier pour la pagination
    document.addEventListener('keydown', function(e) {
        const currentPagination = document.querySelector('.pagination');
        if (!currentPagination) return;
        
        const prevLink = currentPagination.querySelector('[rel="prev"]');
        const nextLink = currentPagination.querySelector('[rel="next"]');
        
        // Ctrl + Flèche gauche = page précédente
        if (e.ctrlKey && e.key === 'ArrowLeft' && prevLink) {
            e.preventDefault();
            prevLink.click();
        }
        
        // Ctrl + Flèche droite = page suivante
        if (e.ctrlKey && e.key === 'ArrowRight' && nextLink) {
            e.preventDefault();
            nextLink.click();
        }
    });
});

/**
 * Fonction utilitaire pour afficher/masquer un indicateur de chargement
 */
function showPaginationLoader() {
    const loader = document.getElementById('paginationLoader');
    if (loader) {
        loader.classList.remove('d-none');
    }
}

function hidePaginationLoader() {
    const loader = document.getElementById('paginationLoader');
    if (loader) {
        loader.classList.add('d-none');
    }
}

/**
 * Fonction pour mettre à jour la pagination via AJAX (optionnel)
 */
function loadPage(url, targetSelector = '.table-responsive') {
    showPaginationLoader();
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector(targetSelector);
        const newPagination = doc.querySelector('nav[role="navigation"]');
        
        if (newContent) {
            document.querySelector(targetSelector).innerHTML = newContent.innerHTML;
        }
        
        if (newPagination) {
            const currentPagination = document.querySelector('nav[role="navigation"]');
            if (currentPagination) {
                currentPagination.innerHTML = newPagination.innerHTML;
            }
        }
        
        // Réinitialiser les event listeners
        initPaginationListeners();
        
        // Scroll vers le haut du contenu
        document.querySelector(targetSelector).scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
        
        hidePaginationLoader();
    })
    .catch(error => {
        console.error('Erreur lors du chargement de la page:', error);
        hidePaginationLoader();
        // Fallback: navigation normale
        window.location.href = url;
    });
}

/**
 * Réinitialiser les event listeners après mise à jour AJAX
 */
function initPaginationListeners() {
    const paginationLinks = document.querySelectorAll('.pagination a[data-ajax="true"]');
    
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            loadPage(this.href);
        });
    });
}

// Styles CSS pour les animations
const style = document.createElement('style');
style.textContent = `
    .pagination-animated .page-link {
        transition: all 0.2s ease-in-out;
    }
    
    .pagination-animated .page-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .pagination-animated .page-item.active .page-link {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    .pagination-loader {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    @media (max-width: 576px) {
        .pagination {
            justify-content: center;
        }
        
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.875rem;
        }
    }
    
    /* Animations d'entrée pour les nouveaux éléments */
    .table tbody tr {
        animation: fadeInUp 0.3s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;

document.head.appendChild(style);