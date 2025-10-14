@if ($paginator->hasPages())
<div class="pagination-wrapper">
    <nav class="pagination-nav" aria-label="Navigation par pages">
        <ul class="pagination-list">
            {{-- Bouton Précédent --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item disabled">
                    <span class="pagination-link" aria-hidden="true">
                        <i class="bi bi-chevron-left"></i>
                        <span class="pagination-text d-none d-sm-inline">Précédent</span>
                    </span>
                </li>
            @else
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="bi bi-chevron-left"></i>
                        <span class="pagination-text d-none d-sm-inline">Précédent</span>
                    </a>
                </li>
            @endif

            {{-- Éléments de pagination --}}
            @foreach ($elements as $element)
                {{-- "Trois points" Separator --}}
                @if (is_string($element))
                    <li class="pagination-item disabled">
                        <span class="pagination-link pagination-dots">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array de liens --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pagination-item active" aria-current="page">
                                <span class="pagination-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="pagination-item">
                                <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Bouton Suivant --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item">
                    <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Page suivante">
                        <span class="pagination-text d-none d-sm-inline">Suivant</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="pagination-item disabled">
                    <span class="pagination-link" aria-hidden="true">
                        <span class="pagination-text d-none d-sm-inline">Suivant</span>
                        <i class="bi bi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
    </nav>

    {{-- Informations sur la pagination --}}
    <div class="pagination-info">
        <span class="pagination-results">
            Affichage de {{ $paginator->firstItem() ?? 0 }} à {{ $paginator->lastItem() ?? 0 }} 
            sur {{ $paginator->total() }} résultats
        </span>
    </div>
</div>

<style>
/* Variables pour la pagination */
:root {
    --pagination-primary: #10b981;
    --pagination-primary-dark: #047857;
    --pagination-bg: #ffffff;
    --pagination-border: #e5e7eb;
    --pagination-hover: #f3f4f6;
    --pagination-disabled: #9ca3af;
    --pagination-text: #374151;
    --pagination-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --pagination-radius: 0.75rem;
}

/* Container principal */
.pagination-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
    padding: 1rem;
}

/* Navigation pagination */
.pagination-nav {
    display: flex;
    justify-content: center;
}

.pagination-list {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    list-style: none;
    margin: 0;
    padding: 0;
    background: var(--pagination-bg);
    border-radius: var(--pagination-radius);
    box-shadow: var(--pagination-shadow);
    border: 1px solid var(--pagination-border);
    overflow: hidden;
}

/* Items de pagination */
.pagination-item {
    display: flex;
}

.pagination-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: var(--pagination-text);
    text-decoration: none;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    border: none;
    background: transparent;
    cursor: pointer;
    min-width: 44px;
    justify-content: center;
    position: relative;
}

.pagination-link:hover {
    background: var(--pagination-hover);
    color: var(--pagination-primary);
    transform: translateY(-1px);
}

/* Page active */
.pagination-item.active .pagination-link {
    background: var(--pagination-primary);
    color: white;
    font-weight: 700;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.3);
}

.pagination-item.active .pagination-link:hover {
    background: var(--pagination-primary-dark);
    transform: none;
}

/* État désactivé */
.pagination-item.disabled .pagination-link {
    color: var(--pagination-disabled);
    cursor: not-allowed;
    background: transparent;
}

.pagination-item.disabled .pagination-link:hover {
    background: transparent;
    transform: none;
}

/* Points de suspension */
.pagination-dots {
    padding: 0.75rem 0.5rem;
    color: var(--pagination-disabled);
}

/* Icônes */
.pagination-link i {
    font-size: 0.875rem;
}

/* Informations de pagination */
.pagination-info {
    text-align: center;
    margin-top: 0.5rem;
}

.pagination-results {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 640px) {
    .pagination-wrapper {
        margin: 1rem 0;
        padding: 0.5rem;
    }
    
    .pagination-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
        min-width: 36px;
    }
    
    .pagination-text {
        display: none !important;
    }
    
    /* Masquer certains éléments sur mobile */
    .pagination-item:nth-child(n+6):nth-last-child(n+4) {
        display: none;
    }
    
    .pagination-results {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .pagination-list {
        gap: 0.125rem;
    }
    
    .pagination-link {
        padding: 0.5rem;
        min-width: 32px;
    }
    
    /* Afficher seulement quelques pages sur très petits écrans */
    .pagination-item:nth-child(n+4):nth-last-child(n+4) {
        display: none;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.pagination-wrapper {
    animation: fadeIn 0.3s ease;
}

/* États de focus pour l'accessibilité */
.pagination-link:focus {
    outline: 2px solid var(--pagination-primary);
    outline-offset: 2px;
    background: var(--pagination-hover);
}

.pagination-item.active .pagination-link:focus {
    outline-color: white;
}

/* Version sombre (optionnel) */
@media (prefers-color-scheme: dark) {
    :root {
        --pagination-bg: #1f2937;
        --pagination-border: #374151;
        --pagination-hover: #374151;
        --pagination-disabled: #6b7280;
        --pagination-text: #d1d5db;
        --pagination-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
}
</style>
@endif