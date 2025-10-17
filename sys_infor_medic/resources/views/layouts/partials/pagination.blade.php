{{-- Pagination personnalisée moderne --}}
@if ($paginator->hasPages())
    <nav class="d-flex justify-content-between align-items-center" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
        {{-- Informations de pagination --}}
        <div class="pagination-info d-none d-sm-flex">
            <p class="small text-muted mb-0">
                {!! __('Affichage de') !!}
                <span class="fw-medium">{{ $paginator->firstItem() }}</span>
                {!! __('à') !!}
                <span class="fw-medium">{{ $paginator->lastItem() }}</span>
                {!! __('sur') !!}
                <span class="fw-medium">{{ $paginator->total() }}</span>
                {!! __('résultats') !!}
            </p>
        </div>

        {{-- Pagination compacte pour mobile --}}
        <div class="d-flex d-sm-none">
            @if ($paginator->onFirstPage())
                <span class="btn btn-outline-secondary btn-sm disabled me-2">
                    <i class="bi bi-chevron-left"></i>
                </span>
            @else
                <a class="btn btn-outline-primary btn-sm me-2" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    <i class="bi bi-chevron-left"></i>
                </a>
            @endif

            <span class="btn btn-light btn-sm me-2">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a class="btn btn-outline-primary btn-sm" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    <i class="bi bi-chevron-right"></i>
                </a>
            @else
                <span class="btn btn-outline-secondary btn-sm disabled">
                    <i class="bi bi-chevron-right"></i>
                </span>
            @endif
        </div>

        {{-- Pagination complète pour desktop --}}
        <div class="d-none d-sm-flex">
            <ul class="pagination pagination-sm mb-0">
                {{-- Bouton Première page --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-double-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}" rel="first" title="Première page">
                            <i class="bi bi-chevron-double-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Bouton Page précédente --}}
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" title="Page précédente">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Numéros de pages --}}
                @php
                    $start = max($paginator->currentPage() - 2, 1);
                    $end = min($start + 4, $paginator->lastPage());
                    $start = max($end - 4, 1);
                @endphp

                @if($start > 1)
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url(1) }}">1</a>
                    </li>
                    @if($start > 2)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                @endif

                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $i }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                @if($end < $paginator->lastPage())
                    @if($end < $paginator->lastPage() - 1)
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    @endif
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}">{{ $paginator->lastPage() }}</a>
                    </li>
                @endif

                {{-- Bouton Page suivante --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" title="Page suivante">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>
                @endif

                {{-- Bouton Dernière page --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->url($paginator->lastPage()) }}" title="Dernière page">
                            <i class="bi bi-chevron-double-right"></i>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">
                            <i class="bi bi-chevron-double-right"></i>
                        </span>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
@endif

<style>
.pagination .page-link {
    border-radius: 6px;
    margin: 0 2px;
    color: #6c757d;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #0d6efd;
    transform: translateY(-1px);
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
    font-weight: 600;
}

.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.pagination-info {
    font-size: 0.875rem;
}

@media (max-width: 576px) {
    .pagination-info {
        font-size: 0.75rem;
    }
}
</style>