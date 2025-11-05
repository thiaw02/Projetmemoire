@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Créer un service</h5>
    <a href="{{ route('admin.services.index') }}" class="btn btn-light btn-sm">Retour</a>
  </div>
  <div class="card-body">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.services.store') }}" method="POST" class="row g-3">
      @csrf
      <div class="col-md-6">
        <label class="form-label">Nom</label>
        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control">{{ old('description') }}</textarea>
      </div>
      <div class="col-12 form-check">
        <input class="form-check-input" type="checkbox" value="1" id="active" name="active" checked>
        <label class="form-check-label" for="active">Actif</label>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-success">Enregistrer</button>
        <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Annuler</a>
      </div>
    </form>
  </div>
</div>
@endsection
