@extends('layouts.app')

@section('content')
<style>
  .rdv-header-btn { border: 2px solid rgba(13,110,253,.15); background: #ffffff; color: #0d6efd; font-weight: 600; border-radius: 10px; }
  .rdv-header-btn:hover { background: #f8fafc; color: #0a58ca; transform: translateY(-1px); }
  .status-chips .btn { border-radius: 999px; padding: .35rem .9rem; font-weight: 600; }
  .status-chips .btn-light { background: #ffffff; border-color: #e5e7eb; color: #111827; }
  .status-chips .btn-outline-light { border-color: rgba(255,255,255,.6)!important; }
  .status-badge { border-radius: 999px; padding: .4rem .7rem; font-weight: 600; letter-spacing: .2px; display: inline-flex; align-items: center; gap: .4rem; }
  .status-badge.pending { background:#fff7ed; color:#9a3412; border:1px solid #fed7aa; }
  .status-badge.confirmed { background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; }
  .status-badge.done { background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }
  .rdv-patient { display: flex; align-items: center; gap: .75rem; }
  .rdv-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg,#22c55e,#16a34a); color: #fff; font-weight: 700; }
  .rdv-name { font-weight: 600; color: #111827; }
  .rdv-sub { font-size: .8rem; color: #6b7280; }
  .badge-time { background:#eef2ff; color:#3730a3; border:1px solid #e0e7ff; }
  .badge-date { background:#ecfeff; color:#0369a1; border:1px solid #cffafe; }
  .rdv-actions .btn { border-width:2px; }
  .modern-card { border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,.06); }
  .modern-card-header { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: #fff; padding: 1rem 1.25rem; }
  .modern-card-body .table { border-radius: 10px; overflow: hidden; }
  .sub-toolbar { background: #f8fafc; border-bottom: 1px solid #e5e7eb; padding: .75rem 1.25rem; }
</style>
<div class="row">
  <div class="col-lg-10 mx-auto">
    <div class="modern-card card-rdv mb-3">
      <div class="modern-card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-calendar-week"></i>
          <span class="fw-semibold">Tous les rendez-vous</span>
        </div>
        <a href="{{ route('infirmier.dashboard') }}" class="btn btn-sm rdv-header-btn">
          <i class="bi bi-arrow-left-circle me-1"></i> Retour Dashboard
        </a>
      </div>
      <div class="sub-toolbar d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="status-chips btn-group" role="group">
          <a href="{{ route('infirmier.rendezvous.index', ['status'=>'all']) }}" class="btn {{ $status==='all' ? 'btn-light' : 'btn-outline-light' }}">Tous</a>
          <a href="{{ route('infirmier.rendezvous.index', ['status'=>'attente']) }}" class="btn {{ $status==='attente' ? 'btn-light' : 'btn-outline-light' }}">En attente</a>
          <a href="{{ route('infirmier.rendezvous.index', ['status'=>'cours']) }}" class="btn {{ $status==='cours' ? 'btn-light' : 'btn-outline-light' }}">En cours</a>
          <a href="{{ route('infirmier.rendezvous.index', ['status'=>'traites']) }}" class="btn {{ $status==='traites' ? 'btn-light' : 'btn-outline-light' }}">Traités</a>
        </div>
      </div>
      <div class="modern-card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Patient</th>
                <th>Médecin</th>
                <th>Date</th>
                <th>Heure</th>
                <th>Statut</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
            @forelse($rendezvous as $rdv)
              <tr>
                <td>{{ optional($rdv->patient)->nom }} {{ optional($rdv->patient)->prenom }}</td>
                <td>{{ optional($rdv->medecin)->name }}</td>
                <td>{{ \Carbon\Carbon::parse($rdv->date)->format('d/m/Y') }}</td>
                <td>{{ $rdv->heure }}</td>
                <td>
                  @php($s = strtolower($rdv->statut))
                  @php($cls = in_array($s,['terminé','termine','completed']) ? 'done' : (in_array($s,['confirmé','confirme','confirmée','confirmee']) ? 'confirmed' : 'pending'))
                  <span class="status-badge {{ $cls }}">
                    @if($cls==='done')
                      <i class="bi bi-check2-all"></i>
                    @elseif($cls==='confirmed')
                      <i class="bi bi-check-circle"></i>
                    @else
                      <i class="bi bi-hourglass-split"></i>
                    @endif
                    {{ ucfirst($rdv->statut) }}
                  </span>
                </td>
                <td class="text-end">
                  <a href="{{ route('infirmier.suivis.create', ['patient_id'=>optional($rdv->patient)->id, 'rdv_id'=>$rdv->id]) }}" class="btn btn-sm btn-outline-success" title="Suivi patient">
                    <i class="bi bi-heart-pulse"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted py-4">Aucun rendez-vous.</td>
              </tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="modern-card-body pt-0">
        <div class="d-flex justify-content-center">
          {{ $rendezvous->links('pagination.custom') }}
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection
