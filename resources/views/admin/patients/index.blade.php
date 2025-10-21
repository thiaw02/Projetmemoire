@extends('layouts.app')

@section('body_class', 'admin-page')

@section('content')
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="admin-intelligent-sidebar sidebar-standardized">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">
    <div class="admin-main-content">
<style>
  /* Styles admin patients */
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  
  .admin-page-header {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    padding: 1.5rem 2rem;
    border-radius: 16px;
    margin-bottom: 2rem;
    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.15);
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .page-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.4rem;
    font-weight: 600;
    margin: 0;
  }
  
  .page-title i {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.5rem;
    border-radius: 10px;
  }
  
  .back-btn-admin {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
  }
  
  .back-btn-admin:hover {
    background: white;
    color: #dc2626;
    transform: translateY(-1px);
  }
  
  .filters-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
  }
  
  .admin-table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
  }
  
  .admin-table th {
    background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
    color: #991b1b;
    font-weight: 600;
    padding: 1rem 0.75rem;
    border: none;
    font-size: 0.85rem;
  }
  
  .admin-table td {
    padding: 0.75rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  
  .admin-table tbody tr:hover {
    background: #fef2f2;
  }
</style>

<div class="admin-page-header scroll-fade-in">
  <div class="header-content">
    <h4 class="page-title">
      <i class="bi bi-person-hearts"></i>
      Gestion des Patients
    </h4>
    <a href="{{ route('admin.dashboard') }}" class="back-btn-admin">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

<div class="filters-section scroll-slide-left">
  <div class="d-flex justify-content-between align-items-center">
    <form method="GET" class="d-flex gap-3 align-items-center" role="search">
      <div>
        <input type="text" name="q" class="form-control form-control-sm" placeholder="Rechercher patient..." value="{{ request('q') }}" style="min-width: 200px;">
      </div>
      <div>
        <select name="active" class="form-select form-select-sm" style="min-width: 120px;">
          @php($a = request('active','all'))
          <option value="all" {{ $a==='all'?'selected':'' }}>Tous statuts</option>
          <option value="1" {{ $a==='1'?'selected':'' }}>Actifs</option>
          <option value="0" {{ $a==='0'?'selected':'' }}>Inactifs</option>
        </select>
      </div>
      <button class="btn btn-outline-primary btn-sm">
        <i class="bi bi-funnel me-1"></i>Filtrer
      </button>
    </form>
    
    <div class="d-flex gap-2">
      <a href="{{ route('admin.patients.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Ajouter
      </a>
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-list-ul me-1"></i>Listes avancées
      </a>
    </div>
  </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="admin-table-container scroll-scale-in">
  <table class="table admin-table">
      <thead>
          <tr>
              <th><i class="bi bi-person me-1"></i>Nom</th>
              <th><i class="bi bi-envelope me-1"></i>Email</th>
              <th><i class="bi bi-toggle-on me-1"></i>Statut</th>
              <th><i class="bi bi-gear me-1"></i>Actions</th>
          </tr>
      </thead>
      <tbody>
          @foreach($patients as $patient)
          <tr>
              <td>{{ $patient->name }}</td>
              <td>{{ $patient->email }}</td>
              <td>
                <form method="POST" action="{{ route('admin.users.updateActive', $patient->id) }}" class="mb-0">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="active" value="{{ $patient->active ? 0 : 1 }}">
                  <button class="btn btn-sm {{ $patient->active ? 'btn-success' : 'btn-outline-secondary' }}">
                    {{ $patient->active ? 'Actif' : 'Inactif' }}
                  </button>
                </form>
              </td>
              <td>
                <div class="d-flex gap-1">
                  <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-outline-primary btn-sm" title="Modifier">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?');" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-outline-danger btn-sm" title="Supprimer">
                        <i class="bi bi-trash"></i>
                      </button>
                  </form>
                </div>
              </td>
          </tr>
          @endforeach
      </tbody>
  </table>
</div>

    </div> {{-- Fin admin-main-content --}}
  </div> {{-- Fin col-lg-9 --}}
</div> {{-- Fin row --}}
@endsection
