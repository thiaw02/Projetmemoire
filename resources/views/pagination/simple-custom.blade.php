@if ($paginator->hasPages())
<div class="simple-pagination-wrapper">
    <nav class="simple-pagination-nav" aria-label="Navigation simple">
        <div class="simple-pagination-content">
            {{-- Bouton Précédent --}}
            @if ($paginator->onFirstPage())
                <span class="simple-pagination-link disabled">
                    <i class="bi bi-chevron-left"></i>
                    Précédent
                </span>
            @else
                <a class="simple-pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                    Précédent
                </a>
            @endif

            {{-- Informations de page --}}
            <div class="simple-pagination-info">
                <span class="simple-pagination-current">Page {{ $paginator->currentPage() }}</span>
                <span class="simple-pagination-separator">de</span>
                <span class="simple-pagination-total">{{ $paginator->lastPage() }}</span>
            </div>

            {{-- Bouton Suivant --}}
            @if ($paginator->hasMorePages())
                <a class="simple-pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    Suivant
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span class="simple-pagination-link disabled">
                    Suivant
                    <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
</div>

<style>
/* Variables pour pagination simple */
:root {
    --simple-pagination-primary: #10b981;
    --simple-pagination-primary-dark: #047857;
    --simple-pagination-bg: #ffffff;
    --simple-pagination-border: #e5e7eb;
    --simple-pagination-hover: #f3f4f6;
    --simple-pagination-disabled: #9ca3af;
    --simple-pagination-text: #374151;
    --simple-pagination-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --simple-pagination-radius: 0.75rem;
}

.simple-pagination-wrapper {
    display: flex;
    justify-content: center;
    margin: 2rem 0;
    padding: 1rem;
}

.simple-pagination-nav {
    display: flex;
    justify-content: center;
}

.simple-pagination-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--simple-pagination-bg);
    border-radius: var(--simple-pagination-radius);
    box-shadow: var(--simple-pagination-shadow);
    border: 1px solid var(--simple-pagination-border);
    padding: 0.75rem 1.5rem;
}

.simple-pagination-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    color: var(--simple-pagination-primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: calc(var(--simple-pagination-radius) - 2px);
    transition: all 0.2s ease;
    cursor: pointer;
}

.simple-pagination-link:hover {
    background: var(--simple-pagination-hover);
    color: var(--simple-pagination-primary-dark);
    transform: translateY(-1px);
}

.simple-pagination-link.disabled {
    color: var(--simple-pagination-disabled);
    cursor: not-allowed;
    background: transparent;
}

.simple-pagination-link.disabled:hover {
    background: transparent;
    transform: none;
}

.simple-pagination-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0 1rem;
    border-left: 1px solid var(--simple-pagination-border);
    border-right: 1px solid var(--simple-pagination-border);
    color: var(--simple-pagination-text);
    font-size: 0.875rem;
}

.simple-pagination-current {
    font-weight: 700;
    color: var(--simple-pagination-primary);
}

.simple-pagination-separator {
    color: var(--simple-pagination-disabled);
    margin: 0 0.25rem;
}

.simple-pagination-total {
    font-weight: 500;
}

/* Responsive */
@media (max-width: 640px) {
    .simple-pagination-wrapper {
        margin: 1rem 0;
        padding: 0.5rem;
    }

    .simple-pagination-content {
        padding: 0.5rem 1rem;
        gap: 0.75rem;
    }

    .simple-pagination-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }

    .simple-pagination-info {
        padding: 0 0.75rem;
        font-size: 0.8rem;
    }
}

/* Animation */
@keyframes simpleFadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.simple-pagination-wrapper {
    animation: simpleFadeIn 0.3s ease;
}

/* États de focus */
.simple-pagination-link:focus {
    outline: 2px solid var(--simple-pagination-primary);
    outline-offset: 2px;
}

/* Version sombre */
@media (prefers-color-scheme: dark) {
    :root {
        --simple-pagination-bg: #1f2937;
        --simple-pagination-border: #374151;
        --simple-pagination-hover: #374151;
        --simple-pagination-disabled: #6b7280;
        --simple-pagination-text: #d1d5db;
        --simple-pagination-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
}
</style>
@endif