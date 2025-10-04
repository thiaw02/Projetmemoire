@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .admin-header { position: sticky; top: 0; background: #fff; z-index: 10; padding-top: .25rem; border-bottom: 1px solid rgba(0,0,0,.05); }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  .quick-actions .btn { min-width: 220px; }
</style>
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

<div class="admin-header d-flex align-items-center justify-content-between mb-3">
  <h2 class="mb-0 text-success">Tableau de Bord Médecin</h2>
  @php
    $unread = \App\Models\Message::whereNull('read_at')->whereHas('conversation', function($q){ $uid = auth()->id(); $q->where('user_one_id',$uid)->orWhere('user_two_id',$uid);})->where('sender_id','!=',auth()->id())->count();
  @endphp
  <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm me-2 position-relative">
    <i class="bi bi-bell"></i>
    @if($unread>0)
      <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unread>0 ? '' : 'd-none' }}">{{ $unread }}</span>
    @endif
  </a>
  <form action="{{ route('logout') }}" method="POST" class="ms-1">
    @csrf
    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Déconnexion</button>
  </form>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <p class="mb-0">Bienvenue, Dr. {{ auth()->user()->name }} ! Voici un résumé rapide de votre activité.</p>
          <input type="text" id="searchMedecin" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
        </div>

        <!-- Boutons principaux -->
        <div class="mb-4 quick-actions d-flex flex-wrap gap-2">
            <a href="{{ route('medecin.consultations') }}" class="btn btn-outline-primary">🩺 Consultations</a>
            <a href="{{ route('medecin.ordonnances') }}" class="btn btn-outline-warning">💊 Ordonnances</a>
            <a href="{{ route('medecin.dossierpatient') }}" class="btn btn-outline-success">📁 Consulter les dossiers</a>
        </div>

        <div class="row">
            <!-- Consultations à venir -->
            <div class="col-md-6 mb-3">
                <div class="card border-success shadow-sm">
                    <div class="card-header bg-success text-white">
                        Consultations à venir
                    </div>
                    <div class="card-body">
                        @if($consultations->isEmpty())
                            <p>Aucune consultation à venir.</p>
                        @else
                            <ul>
                                @foreach($consultations as $rdv)
                                    <li>Patient : {{ $rdv->patient->nom }} - {{ \Carbon\Carbon::parse($rdv->date_consultation)->format('d M Y H:i') }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dossiers récents consultés -->
            <div class="col-md-6 mb-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-header bg-primary text-white">
                        Dossiers récents consultés
                    </div>
                    <div class="card-body">
                        @if($dossiersRecents->isEmpty())
                            <p>Aucun dossier consulté récemment.</p>
                        @else
                            <ul>
                                @foreach($dossiersRecents as $patient)
                                    <li>{{ $patient->nom }} {{ $patient->prenom }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>

</div>
</div>
  </div>
</div>
<script>
  (function(){
    const inp = document.getElementById('searchMedecin');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      document.querySelectorAll('.card .card-body ul li, .card .card-body .card').forEach(el=>{
        const t = el.innerText?.toLowerCase?.() || '';
        if (el.tagName==='LI' || el.classList.contains('card')) {
          el.style.display = t.includes(q) ? '' : 'none';
        }
      });
    }
    inp?.addEventListener('input', filter);
  })();
</script>
@endsection
