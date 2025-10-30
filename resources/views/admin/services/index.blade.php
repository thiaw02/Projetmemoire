@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Services</h5>
    <a href="{{ route('admin.services.create') }}" class="btn btn-success btn-sm">
      <i class="bi bi-plus-lg me-1"></i>Nouveau service
    </a>
  </div>
  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
