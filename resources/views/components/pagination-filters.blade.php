@props([
    'searchPlaceholder' => 'Rechercher...',
    'searchValue' => '',
    'perPageOptions' => [10 => '10', 15 => '15', 25 => '25', 50 => '50', 100 => '100'],
    'currentPerPage' => 15,
    'showPerPageSelector' => true,
    'showSearch' => true,
    'additionalFilters' => [],
    'formAction' => '',
    'showExport' => false,
    'exportUrl' => '',
    'stats' => []
])

<div class="pagination-filters-wrapper">
    <!-- Statistiques en haut (optionnel) -->
    @if (!empty($stats))
        <div class="pagination-stats">
            @foreach ($stats as $stat)
                <div class="stat-item">
                    <span class="stat-number">{{ $stat['value'] }}</span>
                    <span class="stat-label">{{ $stat['label'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Barre de filtres -->
    <div class="pagination-filters-bar">
        <form method="GET" action="{{ $formAction }}" class="filters-form">
            <div class="filters-row">
                <!-- Section recherche -->
                @if ($showSearch)
                    <div class="filter-group search-group">
                        <div class="search-input-wrapper">
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ $searchValue }}" 
                                placeholder="{{ $searchPlaceholder }}"
                                class="search-input"
                                autocomplete="off"
                            >
                            <button type="submit" class="search-button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Filtres additionnels -->
                @if (!empty($additionalFilters))
                    <div class="additional-filters">
                        {{ $additionalFilters }}
                    </div>
                @endif

                <!-- Actions -->
                <div class="filter-actions">
                    @if ($showPerPageSelector)
                        <div class="per-page-selector">
                            <select name="per_page" class="per-page-select" onchange="this.form.submit()">
                                @foreach ($perPageOptions as $value => $label)
                                    <option value="{{ $value }}" {{ $currentPerPage == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- Boutons de contrôle -->
                    <div class="control-buttons">
                        <button type="button" class="btn-reset" onclick="resetFilters()">
                            <i class="bi bi-arrow-clockwise"></i>
                            <span>Réinitialiser</span>
                        </button>

                        @if ($showExport && $exportUrl)
                            <a href="{{ $exportUrl }}" class="btn-export">
                                <i class="bi bi-download"></i>
                                <span>Exporter</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Filtres avancés (cachés par défaut) -->
            <div class="advanced-filters" id="advancedFilters" style="display: none;">
                {{ $slot }}
            </div>

            <!-- Bouton pour afficher/masquer filtres avancés -->
            @if (trim($slot))
                <button type="button" class="toggle-advanced-filters" onclick="toggleAdvancedFilters()">
                    <i class="bi bi-funnel"></i>
                    <span id="advancedFiltersText">Filtres avancés</span>
                    <i class="bi bi-chevron-down" id="advancedFiltersIcon"></i>
                </button>
            @endif
        </form>
    </div>
</div>

<style>
/* Variables pour les filtres de pagination */
:root {
    --filter-bg: #ffffff;
    --filter-border: #e5e7eb;
    --filter-text: #374151;
    --filter-placeholder: #9ca3af;
    --filter-focus: #10b981;
    --filter-hover: #f3f4f6;
    --filter-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --filter-radius: 0.5rem;
}

/* Container principal */
.pagination-filters-wrapper {
    margin-bottom: 1.5rem;
}

/* Statistiques */
.pagination-stats {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: var(--filter-radius);
    border: 1px solid var(--filter-border);
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--filter-focus);
    line-height: 1;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--filter-text);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-top: 0.25rem;
}

/* Barre de filtres */
.pagination-filters-bar {
    background: var(--filter-bg);
    border: 1px solid var(--filter-border);
    border-radius: var(--filter-radius);
    box-shadow: var(--filter-shadow);
    overflow: hidden;
}

.filters-form {
    padding: 1rem;
}

.filters-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Groupe de recherche */
.search-group {
    flex: 1;
    min-width: 250px;
}

.search-input-wrapper {
    position: relative;
    display: flex;
}

.search-input {
    flex: 1;
    padding: 0.75rem 1rem;
    padding-right: 3rem;
    border: 1px solid var(--filter-border);
    border-radius: var(--filter-radius);
    font-size: 0.875rem;
    transition: all 0.2s ease;
    background: var(--filter-bg);
}

.search-input:focus {
    outline: none;
    border-color: var(--filter-focus);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-input::placeholder {
    color: var(--filter-placeholder);
}

.search-button {
    position: absolute;
    right: 0;
    top: 0;
    bottom: 0;
    width: 3rem;
    border: none;
    background: transparent;
    color: var(--filter-placeholder);
    cursor: pointer;
    transition: color 0.2s ease;
}

.search-button:hover {
    color: var(--filter-focus);
}

/* Filtres additionnels */
.additional-filters {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

/* Actions de contrôle */
.filter-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-left: auto;
}

.per-page-selector {
    display: flex;
    align-items: center;
}

.per-page-select {
    padding: 0.5rem 0.75rem;
    border: 1px solid var(--filter-border);
    border-radius: var(--filter-radius);
    font-size: 0.875rem;
    background: var(--filter-bg);
    cursor: pointer;
    min-width: 120px;
}

.per-page-select:focus {
    outline: none;
    border-color: var(--filter-focus);
}

.control-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-reset, .btn-export {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid var(--filter-border);
    border-radius: var(--filter-radius);
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    background: var(--filter-bg);
    color: var(--filter-text);
}

.btn-reset:hover, .btn-export:hover {
    background: var(--filter-hover);
    border-color: var(--filter-focus);
    color: var(--filter-focus);
}

.btn-export {
    background: var(--filter-focus);
    color: white;
    border-color: var(--filter-focus);
}

.btn-export:hover {
    background: #047857;
    border-color: #047857;
    color: white;
}

/* Filtres avancés */
.advanced-filters {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--filter-border);
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.toggle-advanced-filters {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding: 0.5rem;
    background: none;
    border: none;
    color: var(--filter-focus);
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: var(--filter-radius);
}

.toggle-advanced-filters:hover {
    background: var(--filter-hover);
}

.toggle-advanced-filters i:last-child {
    transition: transform 0.3s ease;
}

.toggle-advanced-filters.active i:last-child {
    transform: rotate(180deg);
}

/* Responsive */
@media (max-width: 768px) {
    .pagination-stats {
        flex-wrap: wrap;
        gap: 1rem;
    }

    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }

    .search-group {
        min-width: auto;
    }

    .filter-actions {
        margin-left: 0;
        justify-content: space-between;
    }

    .control-buttons {
        order: 1;
    }

    .per-page-selector {
        order: 2;
    }
}

@media (max-width: 480px) {
    .control-buttons {
        flex-direction: column;
        width: 100%;
    }

    .btn-reset, .btn-export {
        justify-content: center;
        width: 100%;
    }
}

/* Version sombre */
@media (prefers-color-scheme: dark) {
    :root {
        --filter-bg: #1f2937;
        --filter-border: #374151;
        --filter-text: #d1d5db;
        --filter-placeholder: #6b7280;
        --filter-hover: #374151;
        --filter-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
}
</style>

<script>
function resetFilters() {
    // Vider tous les champs de recherche et filtres
    const form = document.querySelector('.filters-form');
    const inputs = form.querySelectorAll('input[type="text"], input[type="email"], input[type="search"]');
    const selects = form.querySelectorAll('select:not(.per-page-select)');
    
    inputs.forEach(input => input.value = '');
    selects.forEach(select => select.selectedIndex = 0);
    
    // Soumettre le formulaire pour appliquer la réinitialisation
    form.submit();
}

function toggleAdvancedFilters() {
    const advancedFilters = document.getElementById('advancedFilters');
    const toggleButton = document.querySelector('.toggle-advanced-filters');
    const toggleText = document.getElementById('advancedFiltersText');
    const toggleIcon = document.getElementById('advancedFiltersIcon');
    
    if (advancedFilters.style.display === 'none') {
        advancedFilters.style.display = 'block';
        toggleButton.classList.add('active');
        toggleText.textContent = 'Masquer les filtres avancés';
    } else {
        advancedFilters.style.display = 'none';
        toggleButton.classList.remove('active');
        toggleText.textContent = 'Filtres avancés';
    }
}

// Auto-soumission pour certains changements
document.addEventListener('DOMContentLoaded', function() {
    const autoSubmitSelects = document.querySelectorAll('select[data-auto-submit="true"]');
    autoSubmitSelects.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>