@php
  $user = auth()->user();
  $rawAvatar = $user->avatar_url ?: ('https://ui-avatars.com/api/?size=120&name=' . urlencode($user->name));
  $avatar = (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://') || str_starts_with($rawAvatar, '//'))
            ? $rawAvatar
            : asset(ltrim($rawAvatar, '/'));
@endphp
<div class="card shadow-sm mb-4">
  <div class="card-body text-center">
    <div class="mb-3">
      <img src="{{ $avatar }}" alt="Photo de profil" class="rounded-circle border" width="120" height="120" style="object-fit:cover;">
    </div>
    <h5 class="mb-1">{{ $user->name }}</h5>
    <div class="text-muted mb-3" style="text-transform: capitalize;">{{ $user->role ?? 'utilisateur' }}</div>
    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm mb-3">Paramètres</a>
    <hr>
    <ul class="list-unstyled text-start small mb-0">
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-envelope me-2"></i>Email</span>
        <span>{{ $user->email }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar3 me-2"></i>Membre depuis</span>
        <span>{{ optional($user->created_at)->format('d/m/Y') }}</span>
      </li>

      @if(($user->role ?? '') === 'patient')
      @php $pp = $user->patient; @endphp
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-hash me-2"></i>N° dossier</span>
        <span>{{ $pp->numero_dossier ?? '—' }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-telephone me-2"></i>Téléphone</span>
        <span>{{ $pp->telephone ?? '—' }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-gender-ambiguous me-2"></i>Sexe</span>
        <span>{{ $pp->sexe ?? '—' }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-event me-2"></i>Naissance</span>
        <span>{{ optional($pp->date_naissance)->format('d/m/Y') }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-droplet me-2"></i>Groupe</span>
        <span>{{ $pp->groupe_sanguin ?? '—' }}</span>
      </li>
      <li class="mb-2">
        <span class="d-block text-muted">Antécédents</span>
        <div>{{ $pp->antecedents ?? '—' }}</div>
      </li>
      @elseif(($user->role ?? '') === 'medecin')
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-briefcase me-2"></i>Spécialité</span>
        <span>{{ $user->specialite ?? '—' }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-upc me-2"></i>Matricule</span>
        <span>{{ $user->matricule ?? '—' }}</span>
      </li>
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-building me-2"></i>Cabinet</span>
        <span>{{ $user->cabinet ?? '—' }}</span>
      </li>
      <li class="mb-2">
        <span class="d-block text-muted"><i class="bi bi-clock me-2"></i>Horaires</span>
        <div>{{ $user->horaires ?? '—' }}</div>
      </li>
      @elseif(in_array(($user->role ?? ''), ['secretaire','infirmier']))
      <li class="mb-2 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-telephone me-2"></i>Tél. Pro</span>
        <span>{{ $user->pro_phone ?? '—' }}</span>
      </li>
      @endif

    </ul>
</div>
</div>
