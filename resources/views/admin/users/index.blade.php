@extends('layouts.app')

@section('body_class', 'admin-page')

@section('content')
{{-- Header moderne pour gestion des utilisateurs --}}
<div class="users-admin-header scroll-fade-in">
  <div class="header-content">
    <div class="header-title">
      <i class="bi bi-people-fill"></i>
      <span>Gestion des Utilisateurs</span>
    </div>
    <div class="header-actions">
      <div class="search-box">
        <i class="bi bi-search"></i>
        <input type="text" id="usersSearch" placeholder="Rechercher un utilisateur..." class="form-control">
      </div>
      <a href="{{ route('admin.dashboard') }}" class="action-btn">
        <i class="bi bi-arrow-left"></i>
        Retour Dashboard
      </a>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

{{-- Barre d’actions (à la place des statistiques) --}}
<div class="d-flex flex-wrap gap-2 mb-4 scroll-slide-left">
  <a href="{{ route('admin.users.create') }}" class="btn btn-success">
    <i class="bi bi-person-plus me-1"></i> Ajouter un utilisateur
  </a>
  <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
    <i class="bi bi-building me-1"></i> Nouveau service
  </button>
  <a href="{{ route('admin.services.index') }}" class="btn btn-outline-primary">
    <i class="bi bi-list-check me-1"></i> Voir services
  </a>
  <a href="{{ route('admin.audit.index') }}" class="btn btn-outline-secondary">
    <i class="bi bi-clipboard-data me-1"></i> Logs d'audit
  </a>
</div>

{{-- Nouveau système de filtres et pagination moderne --}}
<div class="scroll-fade-in">
<x-pagination-filters
    search-placeholder="Rechercher par nom, email..."
    :search-value="$filters['q'] ?? ''"
    :current-per-page="request('per_page', 20)"
    :show-export="true"
    :export-url="route('admin.users.export', request()->query())"
    :stats="[
        ['value' => $users->total(), 'label' => 'Total'],
        ['value' => $users->where('active', 1)->count(), 'label' => 'Actifs'],
        ['value' => $users->where('role', 'medecin')->count(), 'label' => 'Médecins'],
        ['value' => $users->where('role', 'patient')->count(), 'label' => 'Patients']
    ]">
    {{-- Filtres avancés --}}
    <div class="advanced-filters-grid">
        <div class="filter-group">
            <label for="role" class="filter-label">Rôle</label>
            <select name="role" id="role" class="filter-select" data-auto-submit="true">
                @php $r = $filters['role'] ?? 'all'; @endphp
                <option value="all" {{ $r==='all'?'selected':'' }}>Tous les rôles</option>
                <option value="admin" {{ $r==='admin'?'selected':'' }}>Administrateur</option>
                <option value="secretaire" {{ $r==='secretaire'?'selected':'' }}>Secrétaire</option>
                <option value="medecin" {{ $r==='medecin'?'selected':'' }}>Médecin</option>
                <option value="infirmier" {{ $r==='infirmier'?'selected':'' }}>Infirmier</option>
                <option value="patient" {{ $r==='patient'?'selected':'' }}>Patient</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="active" class="filter-label">Statut</label>
            <select name="active" id="active" class="filter-select" data-auto-submit="true">
                @php $a = $filters['active'] ?? 'all'; @endphp
                <option value="all" {{ $a==='all'?'selected':'' }}>Tous les statuts</option>
                <option value="1" {{ $a==='1'?'selected':'' }}>Actifs uniquement</option>
                <option value="0" {{ $a==='0'?'selected':'' }}>Inactifs uniquement</option>
            </select>
        </div>
        
        <div class="filter-group">
            <label for="specialite" class="filter-label">Spécialité</label>
            <input type="text" name="specialite" id="specialite" class="filter-input" 
                   placeholder="Cardiologie, Pédiatrie..." value="{{ request('specialite') }}">
        </div>
    </div>
</x-pagination-filters>
</div>

{{-- Liste des utilisateurs moderne --}}
<div class="users-container scroll-scale-in">
  <div class="users-header-section">
    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des utilisateurs ({{ $users->total() }} utilisateurs)</h5>
    <div class="users-count-info">
      <span class="count-badge">{{ $users->total() }}</span>
    </div>
  </div>
  
  @if($users->isEmpty())
    <div class="empty-state">
      <i class="bi bi-person-x"></i>
      <h5>Aucun utilisateur pour le moment</h5>
      <p>Les utilisateurs du système apparaîtront ici une fois créés.</p>
      <a href="{{ route('admin.users.create') }}" class="btn-create-first">
        <i class="bi bi-person-plus me-2"></i>
        Créer le premier utilisateur
      </a>
    </div>
  @else
    <div class="users-list">
      <div class="table-responsive">
        <table class="table users-table" id="usersTable">
          <thead>
            <tr>
              <th><i class="bi bi-person me-1"></i>Utilisateur</th>
              <th><i class="bi bi-envelope me-1"></i>Contact</th>
              <th><i class="bi bi-person-badge me-1"></i>Rôle & Spécialité</th>
              <th><i class="bi bi-diagram-3 me-1"></i>Relations</th>
              <th><i class="bi bi-toggle-on me-1"></i>Statut</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr data-user="{{ strtolower($user->name . ' ' . $user->email . ' ' . $user->role) }}">
                <td>
                  <div class="user-info-inline">
                    <div class="user-avatar">
                      {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                      <div class="user-name">{{ $user->name }}</div>
                      <div class="user-meta">
                        <small class="text-muted">ID: #{{ $user->id }}</small>
                        @if($user->created_at)
                          <small class="text-muted"> • Créé le {{ $user->created_at->format('d/m/Y') }}</small>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="contact-info">
                    <div class="email">
                      <i class="bi bi-envelope text-muted me-1"></i>
                      {{ $user->email }}
                    </div>
                    @if($user->telephone)
                      <div class="phone">
                        <i class="bi bi-telephone text-muted me-1"></i>
                        {{ $user->telephone }}
                      </div>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="role-info">
                    <div class="role-badge role-{{ $user->role }}">
                      @php
                        $roleIcons = [
                          'admin' => 'bi-shield-check',
                          'medecin' => 'bi-heart-pulse',
                          'secretaire' => 'bi-person-workspace',
                          'infirmier' => 'bi-bandaid',
                          'patient' => 'bi-person'
                        ];
                      @endphp
                      <i class="bi {{ $roleIcons[$user->role] ?? 'bi-person' }}"></i>
                      {{ ucfirst($user->role) }}
                    </div>
                    @if($user->specialite)
                      <div class="specialite">
                        <small class="text-muted">{{ $user->specialite }}</small>
                      </div>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="relations-info">
                    @if($user->role === 'medecin')
                      @php
                        $nursesCount = ($user->nurses ?? collect())->count();
                        $nursesNames = ($user->nurses ?? collect())->pluck('name')->take(3)->implode(', ');
                      @endphp
                      @if($nursesCount > 0)
                        <div class="relation-badge relation-nurse" title="{{ $nursesNames }}{{ $nursesCount > 3 ? ' et ' . ($nursesCount - 3) . ' autres' : '' }}">
                          <i class="bi bi-people"></i>
                          {{ $nursesCount }} infirmier{{ $nursesCount > 1 ? 's' : '' }}
                        </div>
                      @else
                        <span class="text-muted">Aucun infirmier assigné</span>
                      @endif
                    @elseif($user->role === 'infirmier')
                      @php
                        $doctorsCount = ($user->doctors ?? collect())->count();
                        $doctorsNames = ($user->doctors ?? collect())->pluck('name')->take(3)->implode(', ');
                      @endphp
                      @if($doctorsCount > 0)
                        <div class="relation-badge relation-doctor" title="{{ $doctorsNames }}{{ $doctorsCount > 3 ? ' et ' . ($doctorsCount - 3) . ' autres' : '' }}">
                          <i class="bi bi-person-hearts"></i>
                          {{ $doctorsCount }} médecin{{ $doctorsCount > 1 ? 's' : '' }}
                        </div>
                      @else
                        <span class="text-muted">Aucun médecin assigné</span>
                      @endif
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </div>
                </td>
                <td>
                  <div class="status-toggle">
                    <form method="POST" action="{{ route('admin.users.updateActive', $user->id) }}" class="status-form">
                      @csrf
                      @method('PUT')
                      <input type="hidden" name="active" value="{{ $user->active ? 0 : 1 }}">
                      <button type="submit" class="status-btn {{ $user->active ? 'status-active' : 'status-inactive' }}" 
                              title="{{ $user->active ? 'Cliquer pour désactiver' : 'Cliquer pour activer' }}">
                        <i class="bi {{ $user->active ? 'bi-toggle-on' : 'bi-toggle-off' }}"></i>
                        <span>{{ $user->active ? 'Actif' : 'Inactif' }}</span>
                      </button>
                    </form>
                  </div>
                </td>
                <td>
                  <div class="user-actions">
                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                       class="btn-user-action btn-edit" title="Modifier l'utilisateur">
                      <i class="bi bi-pencil"></i>
                    </a>
                    
                    <button type="button" class="btn-user-action btn-delete" 
                            title="Supprimer l'utilisateur" 
                            onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
                      <i class="bi bi-trash"></i>
                    </button>
                    
                    {{-- Menu 3 points retiré pour simplifier les actions visibles --}}
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      
      {{-- Pagination dédiée aux utilisateurs (indépendante de patients) --}}
      <div class="pagination-container">
        {{ $users->appends(request()->except('patients_page'))->links() }}
      </div>
    </div>
  @endif
</div>

{{-- Modal de confirmation de suppression --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modern-modal">
      <div class="modal-header border-0">
        <div class="modal-icon-warning">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <h5 class="mb-3">Confirmer la suppression</h5>
        <p class="text-muted mb-4">Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ? Cette action est définitive et ne peut pas être annulée.</p>
        <div class="modal-actions">
          <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-2"></i>Annuler
          </button>
          <form id="deleteForm" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-modal-confirm">
              <i class="bi bi-trash me-2"></i>Supprimer
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Modal création de service --}}
<div class="modal fade" id="createServiceModal" tabindex="-1" aria-labelledby="createServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content modern-modal">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="createServiceModalLabel">
          <i class="bi bi-building me-2"></i>Créer un nouveau service
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <form method="POST" action="{{ route('admin.services.store') }}">
        @csrf
        <input type="hidden" name="redirect_back" value="1">
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label required">Nom du service</label>
            <input type="text" name="name" class="form-control" required placeholder="Ex: Odontologie" value="{{ old('name') }}">
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Description du service (optionnel)">{{ old('description') }}</textarea>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="service_active" name="active" value="1" checked>
            <label class="form-check-label" for="service_active">Activer ce service</label>
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check2-circle me-1"></i>Créer le service
          </button>
        </div>
      </form>
    </div>
  </div>
  </div>

{{-- Styles modernes complets pour la gestion des utilisateurs --}}
<style>
  /* Styles pour les nouveaux filtres */
  .advanced-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
  }
  
  .filter-group {
    display: flex;
    flex-direction: column;
  }
  
  .filter-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
  }
  
  .filter-select, .filter-input {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    background: #ffffff;
    transition: all 0.2s ease;
  }
  
  .filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
  }
  
  .quick-actions-row {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
  }
  
  .btn-quick-action {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
  }
  
  .btn-quick-action.btn-primary {
    background: #10b981;
    color: white;
  }
  
  .btn-quick-action.btn-primary:hover {
    background: #047857;
    color: white;
    transform: translateY(-2px);
  }
  
  .btn-quick-action.btn-secondary {
    background: #6b7280;
    color: white;
  }
  
  .btn-quick-action.btn-secondary:hover {
    background: #4b5563;
    color: white;
    transform: translateY(-2px);
  }
  /* Conteneur principal */
  body > .container { max-width: 1500px !important; }
  
  /* Header moderne admin users */
  .users-admin-header {
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 25px rgba(30, 64, 175, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 2rem;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .header-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
    border-radius: 10px;
    font-size: 1.2rem;
  }
  
  .header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
  }
  
  .search-box {
    position: relative;
    min-width: 300px;
  }
  
  .search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    z-index: 2;
  }
  
  .search-box .form-control {
    padding-left: 3rem;
    border: none;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.9);
    color: #374151;
  }
  
  .search-box .form-control:focus {
    background: white;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
  }
  
  .action-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .action-btn:hover {
    background: white;
    color: #1e40af;
    transform: translateY(-2px);
  }
  
  /* Grille des statistiques */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
  }
  
  .stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(30, 64, 175, 0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
  }
  
  .stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 32px rgba(30, 64, 175, 0.15);
  }
  
  .stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
  }
  
  .stat-total .stat-icon { background: linear-gradient(135deg, #1e40af, #1d4ed8); }
  .stat-active .stat-icon { background: linear-gradient(135deg, #10b981, #059669); }
  .stat-medecins .stat-icon { background: linear-gradient(135deg, #ef4444, #dc2626); }
  .stat-secretaires .stat-icon { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
  
  .stat-content {
    flex: 1;
  }
  
  .stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    line-height: 1;
    margin-bottom: 4px;
  }
  
  .stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  /* Conteneur actions */
  .actions-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(30, 64, 175, 0.1);
    overflow: hidden;
  }
  
  .actions-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
  }
  
  .actions-body {
    padding: 2rem;
  }
  
  .filters-row {
    margin-bottom: 2rem;
  }
  
  .filters-form {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 1.5rem;
    align-items: end;
  }
  
  .form-group {
    margin-bottom: 0;
  }
  
  .form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: block;
  }
  
  .form-control {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    background: #f9fafb;
  }
  
  .form-control:focus {
    border-color: #1e40af;
    box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
    background: white;
  }
  
  .form-actions {
    display: flex;
    gap: 1rem;
  }
  
  .btn-filter {
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    color: white;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
    color: white;
  }
  
  .btn-reset {
    background: white;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-reset:hover {
    border-color: #1e40af;
    color: #1e40af;
  }
  
  .quick-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
  }
  
  .btn-add-user {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
  }
  
  .btn-add-user:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
    color: white;
  }
  
  .btn-dropdown {
    background: #f3f4f6;
    border: 2px solid #e5e7eb;
    color: #6b7280;
    padding: 0.8rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .btn-dropdown:hover {
    background: white;
    border-color: #1e40af;
    color: #1e40af;
  }
  
  .modern-dropdown {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    padding: 0.5rem 0;
  }
  
  .modern-dropdown .dropdown-item {
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
  }
  
  .modern-dropdown .dropdown-item:hover {
    background: #f8fafc;
    color: #1e40af;
  }
  
  /* Conteneur utilisateurs */
  .users-container {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(30, 64, 175, 0.1);
    overflow: hidden;
  }
  
  .users-header-section {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .users-header-section h5 {
    color: #374151;
    font-weight: 600;
    margin: 0;
  }
  
  .count-badge {
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
  }
  
  /* Table utilisateurs */
  .users-table {
    margin: 0;
  }
  
  .users-table th {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    color: #475569;
    font-weight: 600;
    padding: 1rem 1.5rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .users-table td {
    padding: 1rem 1.5rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .users-table tbody tr:hover {
    background: #f8fafc;
  }
  
  .user-info-inline {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .user-avatar {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
    flex-shrink: 0;
  }
  
  .user-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 1rem;
  }
  
  .user-meta {
    margin-top: 2px;
  }
  
  .contact-info .email {
    margin-bottom: 4px;
    font-weight: 500;
  }
  
  .contact-info .phone {
    font-size: 0.9rem;
    color: #6b7280;
  }
  
  /* Badges de rôle */
  .role-badge {
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    margin-bottom: 4px;
  }
  
  .role-admin {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
  }
  
  .role-medecin {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
  }
  
  .role-secretaire {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
  }
  
  .role-infirmier {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
  }
  
  .role-patient {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
  }
  
  .specialite {
    margin-top: 2px;
  }
  
  /* Badges de relations */
  .relation-badge {
    padding: 0.3rem 0.6rem;
    border-radius: 15px;
    font-size: 0.75rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
  }
  
  .relation-nurse {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fed7aa;
  }
  
  .relation-doctor {
    background: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
  }
  
  /* Statut toggle */
  .status-btn {
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
  }
  
  .status-active {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
  }
  
  .status-inactive {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
  }
  
  .status-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    color: inherit;
  }
  
  /* Actions des utilisateurs */
  .user-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .btn-user-action {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 0.9rem;
  }
  
  .btn-edit {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
  }
  
  .btn-delete {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
  }
  
  .btn-more {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #e5e7eb;
  }
  
  .btn-user-action:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    color: white;
  }
  
  .btn-more:hover {
    background: white;
    border-color: #1e40af;
    color: #1e40af;
  }
  
  /* État vide */
  .empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
  }
  
  .empty-state i {
    font-size: 4rem;
    color: #1e40af;
    margin-bottom: 1.5rem;
    opacity: 0.6;
  }
  
  .empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
    font-weight: 600;
  }
  
  .btn-create-first {
    background: linear-gradient(135deg, #1e40af, #1d4ed8);
    color: white;
    padding: 0.8rem 2rem;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    margin-top: 1rem;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-create-first:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
    color: white;
  }
  
  /* Pagination */
  .pagination-container {
    padding: 2rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: center;
  }
  
  /* Modal moderne */
  .modern-modal {
    border-radius: 16px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  }
  
  .modal-icon-warning {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    margin: 0 auto 1rem;
  }
  
  .modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }
  
  .btn-modal-cancel {
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-modal-cancel:hover {
    background: #e5e7eb;
    color: #374151;
  }
  
  .btn-modal-confirm {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
  }
  
  .btn-modal-confirm:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-2px);
    color: white;
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      text-align: center;
    }
    
    .search-box {
      min-width: 250px;
    }
    
    .filters-form {
      grid-template-columns: 1fr;
      gap: 1rem;
    }
    
    .form-actions {
      flex-direction: column;
    }
    
    .quick-actions {
      flex-direction: column;
      align-items: stretch;
    }
    
    .users-header-section {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
    
    .modal-actions {
      flex-direction: column;
    }
  }
</style>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche d'utilisateurs
    const searchInput = document.getElementById('usersSearch');
    const userRows = document.querySelectorAll('.users-table tbody tr');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            
            userRows.forEach(row => {
                const userData = row.dataset.user;
                row.style.display = userData.includes(query) ? '' : 'none';
            });
        });
    }
    
    // Fonction de confirmation de suppression
    window.confirmDelete = function(userId, userName) {
        const form = document.getElementById('deleteForm');
        const userNameSpan = document.getElementById('userName');
        
        form.action = `/admin/users/${userId}`;
        userNameSpan.textContent = userName;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };
    
    // Fonction de réinitialisation du mot de passe
    window.resetPassword = function(userId) {
        if (confirm('Confirmer la réinitialisation du mot de passe ?')) {
            fetch(`/admin/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    alert('Mot de passe réinitialisé avec succès');
                } else {
                    alert('Erreur lors de la réinitialisation');
                }
            }).catch(error => {
                alert('Erreur de réseau');
            });
        }
    };
    
    // Animation des cartes statistiques au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = `${Array.from(entry.target.parentNode.children).indexOf(entry.target) * 100}ms`;
                entry.target.classList.add('animate-fadeInUp');
            }
        });
    }, observerOptions);
    
    // Observer les cartes de statistiques
    document.querySelectorAll('.stat-card').forEach(card => {
        observer.observe(card);
    });
});

// Styles pour les animations
const additionalStyles = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease forwards;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
</script>
@endsection
