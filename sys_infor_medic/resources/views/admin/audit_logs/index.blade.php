@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Journal d'audit</h3>
  <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">← Utilisateurs</a>
</div>

<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Recherche (action, type)</label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="ex: ordonnance_updated">
      </div>
      <div class="col-md-4">
        <label class="form-label">Utilisateur (acteur)</label>
        <select name="user_id" class="form-select">
          <option value="">Tous</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" {{ request('user_id')==$u->id ? 'selected':'' }}>{{ $u->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-4">
        <button class="btn btn-outline-primary">Filtrer</button>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-header">Résultats ({{ $logs->total() }})</div>
  <div class="card-body table-responsive">
    <table class="table table-striped align-middle">
      <thead>
        <tr>
          <th>Date</th>
          <th>Utilisateur</th>
          <th>Action</th>
          <th>Objet</th>
          <th>Changements</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ optional($log->created_at)->format('d/m/Y H:i:s') }}</td>
            <td>{{ optional(\App\Models\User::find($log->user_id))->name ?? '—' }}</td>
            <td><code>{{ $log->action }}</code></td>
            <td>
              <div class="small text-muted">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</div>
            </td>
            <td>
              @php($chg = $log->changes ?? [])
              @if(isset($chg['before']) || isset($chg['after']))
                <div class="row g-2">
                  <div class="col-md-6">
                    <div class="small text-muted">Avant</div>
                    <ul class="mb-0">
                      @foreach(($chg['before'] ?? []) as $k=>$v)
                        <li><strong>{{ $k }}</strong>: {{ is_array($v) ? json_encode($v) : (string)$v }}</li>
                      @endforeach
                      @if(empty($chg['before']))<li class="text-muted">—</li>@endif
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <div class="small text-muted">Après</div>
                    <ul class="mb-0">
                      @foreach(($chg['after'] ?? []) as $k=>$v)
                        <li><strong>{{ $k }}</strong>: {{ is_array($v) ? json_encode($v) : (string)$v }}</li>
                      @endforeach
                      @if(empty($chg['after']))<li class="text-muted">—</li>@endif
                    </ul>
                  </div>
                </div>
              @else
                <span class="text-muted">—</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Aucun log</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="mt-2">{{ $logs->links() }}</div>
  </div>
</div>
@endsection