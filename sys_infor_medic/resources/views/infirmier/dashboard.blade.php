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

        <div class="mb-4 quick-actions d-flex flex-wrap gap-2">
                <a href="{{ route('suivi.create') }}" class="btn btn-outline-info position-relative qa-btn">
                  📋 Saisir un suivi patient
                  @if(isset($suivis))
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info text-white">{{ $suivis->count() }}</span>
                  @endif
                </a>
                <a href="{{ route('dossier.index') }}" class="btn btn-outline-warning position-relative qa-btn">
                  📁 Mettre à jour un dossier
                  @if(isset($dossiers))
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">{{ $dossiers->count() }}</span>
                  @endif
                </a>
                <a href="{{ route('historique.index') }}" class="btn btn-outline-success position-relative qa-btn">
                  🔍 Voir l’historique des soins
                </a>
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
    // Etat de chargement sur les boutons d’action
    document.querySelectorAll('.quick-actions .qa-btn')?.forEach(btn=>{
      btn.addEventListener('click', function(){
        this.classList.add('disabled');
        const original = this.innerHTML;
        this.dataset.original = original;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Chargement...';
      });
    });
  })();
</script>
@endsection
