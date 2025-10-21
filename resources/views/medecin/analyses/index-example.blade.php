@extends('layouts.app')

@section('content')
<div class="analyses-container">
    {{-- En-tête de page --}}
    <div class="page-header">
        <div class="header-content">
            <h1><i class="bi bi-graph-up-arrow"></i> Mes Analyses</h1>
            <p>Gestion et suivi des analyses de vos patients</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('medecin.analyses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Nouvelle analyse
            </a>
        </div>
    </div>

    {{-- Système de filtres moderne --}}
    <x-pagination-filters 
        search-placeholder="Rechercher par patient, type d'analyse..."
        :search-value="$filters['search'] ?? ''"
        :current-per-page="$filters['per_page'] ?? 20"
        :show-export="true"
        :export-url="route('medecin.analyses.export.csv', request()->query())"
        :stats="[
            ['value' => $stats['total'], 'label' => 'Total'],
            ['value' => $stats['ce_mois'], 'label' => 'Ce mois'],
            ['value' => $stats['en_attente'], 'label' => 'En attente'],
            ['value' => $stats['terminees'], 'label' => 'Terminées']
        ]">
        
        {{-- Filtres avancés --}}
        <div class="advanced-filters-grid">
            <div class="filter-group">
                <label for="patient_id" class="filter-label">Patient</label>
                <select name="patient_id" id="patient_id" class="filter-select" data-auto-submit="true">
                    <option value="">Tous les patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->nom }} {{ $patient->prenom }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-group">
                <label for="type_analyse" class="filter-label">Type d'analyse</label>
                <input type="text" name="type_analyse" id="type_analyse" class="filter-input"
                       placeholder="Hémogramme, Glycémie..." value="{{ request('type_analyse') }}">
            </div>
            
            <div class="filter-group">
                <label for="etat" class="filter-label">État</label>
                <select name="etat" id="etat" class="filter-select" data-auto-submit="true">
                    <option value="">Tous les états</option>
                    <option value="programmee" {{ request('etat') == 'programmee' ? 'selected' : '' }}>Programmée</option>
                    <option value="en_cours" {{ request('etat') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                    <option value="terminee" {{ request('etat') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                    <option value="annulee" {{ request('etat') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="date_debut" class="filter-label">Date début</label>
                <input type="date" name="date_debut" id="date_debut" class="filter-input"
                       value="{{ request('date_debut') }}">
            </div>
            
            <div class="filter-group">
                <label for="date_fin" class="filter-label">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" class="filter-input"
                       value="{{ request('date_fin') }}">
            </div>
        </div>
    </x-pagination-filters>

    {{-- Liste des analyses --}}
    @if($analyses->isEmpty())
        <div class="empty-state">
            <div class="empty-icon">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <h3>Aucune analyse trouvée</h3>
            <p>Commencez par créer votre première analyse pour un patient</p>
            <a href="{{ route('medecin.analyses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Créer une analyse
            </a>
        </div>
    @else
        <div class="analyses-grid">
            @foreach($analyses as $analyse)
                <div class="analyse-card">
                    <div class="analyse-header">
                        <div class="analyse-type">
                            <i class="bi bi-graph-up-arrow"></i>
                            <h4>{{ $analyse->type_analyse }}</h4>
                        </div>
                        <div class="analyse-status">
                            <span class="status-badge status-{{ $analyse->etat }}">
                                {{ ucfirst($analyse->etat) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="analyse-body">
                        <div class="analyse-patient">
                            <i class="bi bi-person"></i>
                            <span>{{ $analyse->patient->nom ?? 'N/A' }} {{ $analyse->patient->prenom ?? '' }}</span>
                        </div>
                        
                        <div class="analyse-date">
                            <i class="bi bi-calendar"></i>
                            <span>{{ $analyse->date_analyse ? \Carbon\Carbon::parse($analyse->date_analyse)->format('d/m/Y') : 'Non programmée' }}</span>
                        </div>
                        
                        @if($analyse->resultats)
                            <div class="analyse-results">
                                <i class="bi bi-clipboard-data"></i>
                                <span>{{ Str::limit($analyse->resultats, 100) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="analyse-actions">
                        <a href="{{ route('medecin.analyses.show', $analyse->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                            Voir
                        </a>
                        <a href="{{ route('medecin.analyses.edit', $analyse->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                            Modifier
                        </a>
                        <form method="POST" action="{{ route('medecin.analyses.destroy', $analyse->id) }}" 
                              class="d-inline" 
                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette analyse ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                                Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination moderne --}}
        <div class="pagination-wrapper">
            {{ $analyses->links('pagination.custom') }}
        </div>
    @endif
</div>

{{-- Styles CSS spécifiques --}}
<style>
.analyses-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e5e7eb;
}

.header-content h1 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.header-content p {
    color: #6b7280;
    margin: 0.5rem 0 0 0;
}

.analyses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.analyse-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.2s ease;
}

.analyse-card:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.analyse-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.analyse-type {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.analyse-type i {
    color: #10b981;
    font-size: 1.2rem;
}

.analyse-type h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-programmee { background: #fef3c7; color: #92400e; }
.status-en_cours { background: #dbeafe; color: #1e40af; }
.status-terminee { background: #d1fae5; color: #065f46; }
.status-annulee { background: #fee2e2; color: #991b1b; }

.analyse-body {
    margin-bottom: 1rem;
}

.analyse-body > div {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    color: #4b5563;
    font-size: 0.9rem;
}

.analyse-body i {
    color: #9ca3af;
}

.analyse-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    border: 1px solid;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.btn-primary:hover {
    background: #047857;
    border-color: #047857;
    color: white;
}

.btn-outline-primary {
    background: transparent;
    color: #10b981;
    border-color: #10b981;
}

.btn-outline-primary:hover {
    background: #10b981;
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    color: #6b7280;
    border-color: #6b7280;
}

.btn-outline-secondary:hover {
    background: #6b7280;
    color: white;
}

.btn-outline-danger {
    background: transparent;
    color: #ef4444;
    border-color: #ef4444;
}

.btn-outline-danger:hover {
    background: #ef4444;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

.empty-icon {
    margin-bottom: 1rem;
}

.empty-icon i {
    font-size: 3rem;
    color: #9ca3af;
}

.empty-state h3 {
    color: #1f2937;
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: #6b7280;
    margin-bottom: 2rem;
}

/* Responsive */
@media (max-width: 768px) {
    .analyses-container {
        padding: 1rem;
    }
    
    .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .analyses-grid {
        grid-template-columns: 1fr;
    }
    
    .analyse-actions {
        justify-content: center;
    }
}
</style>
@endsection