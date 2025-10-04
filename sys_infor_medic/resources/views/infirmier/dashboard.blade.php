@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .admin-header { position: sticky; top: 0; background: #fff; z-index: 10; padding-top: .25rem; border-bottom: 1px solid rgba(0,0,0,.05); }
  .sidebar-sticky { position: sticky; top: 1rem; }
  .sidebar-sticky img[alt="Photo de profil"] { width: 96px !important; height: 96px !important; }
  .quick-actions .btn { min-width: 230px; }
</style>
<div class="row">
  <div class="col-lg-3 mb-4">
    <div class="sidebar-sticky">
      @include('layouts.partials.profile_sidebar')
    </div>
  </div>
  <div class="col-lg-9">

    <div class="admin-header d-flex align-items-center justify-content-between mb-3">
      <h2 class="mb-0 text-primary">Tableau de Bord Infirmier</h2>
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
                <p class="mb-0">Bienvenue, {{ Auth::user()->name ?? 'infirmier' }} ! Voici un aperçu de vos activités.</p>
                <input type="text" id="searchInfirmier" class="form-control form-control-sm" placeholder="Rechercher..." style="max-width: 240px;">
            </div>

            <div class="row">
                <!-- Suivis en cours -->
                <div class="col-md-6 mb-3">
                    <div class="card border-info shadow-sm">
                        <div class="card-header bg-info text-white">
                            Suivis en cours
                        </div>
                        <div class="card-body">
                            <ul>
                                @forelse($suivis as $suivi)
                                    <li>
                                        Patient :
                                        {{ $suivi->patient->nom ?? 'Inconnu' }}
                                        {{ $suivi->patient->prenom ?? '' }}
                                        - Température : {{ $suivi->temperature ?? 'N/A' }}°C
                                        - Tension : {{ $suivi->tension ?? 'N/A' }}
                                    </li>
                                @empty
                                    <li>Aucun suivi en cours</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Dossiers à mettre à jour -->
                <div class="col-md-6 mb-3">
                    <div class="card border-warning shadow-sm">
                        <div class="card-header bg-warning text-white">
                            Dossiers à mettre à jour
                        </div>
                        <div class="card-body">
                            <ul>
                                @forelse($dossiers as $dossier)
                                    <li>
                                        {{ $dossier->patient->nom ?? 'Inconnu' }}
                                        {{ $dossier->patient->prenom ?? '' }}
                                        - {{ $dossier->observation ?? 'Observation manquante' }}
                                    </li>
                                @empty
                                    <li>Aucun dossier en attente</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="quick-actions d-flex flex-wrap gap-2">
                <a href="{{ route('suivi.create') }}" class="btn btn-outline-info">📋 Saisir un suivi patient</a>
                <a href="{{ route('dossier.index') }}" class="btn btn-outline-warning">📁 Mettre à jour un dossier</a>
                <a href="{{ route('historique.index') }}" class="btn btn-outline-success">🔍 Voir l’historique des soins</a>
            </div>
        </div>
    </div>
  </div>
</div>
<script>
  (function(){
    const inp = document.getElementById('searchInfirmier');
    function filter(){
      const q = (inp?.value || '').toLowerCase();
      document.querySelectorAll('.card .card-body ul li').forEach(li=>{
        li.style.display = li.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    }
    inp?.addEventListener('input', filter);
  })();
</script>
@endsection
