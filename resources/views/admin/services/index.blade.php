@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Services</h5>
    <div class="d-flex align-items-center gap-2">
      <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Retour
      </a>
      <a href="{{ route('admin.services.create') }}" class="btn btn-success btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Nouveau service
      </a>
    </div>
  </div>
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
      <span class="badge bg-primary">Total: {{ $totalServices }}</span>
    </div>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Nom</th>
            <th>Actif</th>
            <th>Créé le</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($services as $s)
          <tr>
            <td>{{ $s->id }}</td>
            <td>{{ $s->name }}</td>
            <td>
              <span class="badge {{ $s->active ? 'bg-success' : 'bg-secondary' }}">
                {{ $s->active ? 'Actif' : 'Inactif' }}
              </span>
            </td>
            <td>{{ optional($s->created_at)->format('Y-m-d H:i') }}</td>
            <td class="text-end">
              <a href="{{ route('admin.services.edit', $s) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i>
              </a>
              <form action="{{ route('admin.services.destroy', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce service ?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
          <tr>
            <td colspan="5">
              <div class="p-2 bg-light rounded">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div><strong>Utilisateurs</strong> <span class="badge bg-info">{{ $s->users_count }}</span></div>
                </div>
                @if($s->users->isEmpty())
                  <div class="text-muted">Aucun utilisateur dans ce service.</div>
                @else
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead>
                        <tr>
                          <th>Nom</th>
                          <th>Email</th>
                          <th>Rôle</th>
                          <th style="width:340px;" class="text-end">Changer de service</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($s->users as $u)
                          <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="badge bg-secondary text-uppercase">{{ $u->role }}</span></td>
                            <td class="text-end">
                              <form action="{{ route('admin.services.change-user-service') }}" method="POST" class="d-inline-flex gap-2 justify-content-end">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                <select name="service_id" class="form-select form-select-sm" style="max-width: 220px;">
                                  <option value="">Aucun</option>
                                  @foreach($allServices as $opt)
                                    <option value="{{ $opt->id }}" @selected($u->service_id == $opt->id)>{{ $opt->name }}</option>
                                  @endforeach
                                </select>
                                <button class="btn btn-sm btn-outline-primary">Changer</button>
                              </form>
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted">Aucun service</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    {{ $services->links() }}
  </div>
</div>
@endsection

