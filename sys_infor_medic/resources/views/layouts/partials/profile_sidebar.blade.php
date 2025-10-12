@php
  $user = auth()->user();
  $rawAvatar = $user->avatar_url ?: ('https://ui-avatars.com/api/?size=120&name=' . urlencode($user->name));
  $avatar = (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://') || str_starts_with($rawAvatar, '//'))
            ? $rawAvatar
            : asset(ltrim($rawAvatar, '/'));
@endphp

@once
  <link rel="stylesheet" href="{{ asset('css/profile-sidebar.css') }}">
@endonce

<div class="modern-sidebar role-{{ $user->role ?? 'user' }}">
  <div class="sidebar-body text-center">
    <div class="profile-avatar">
      <img src="{{ $avatar }}" alt="Photo de profil" class="rounded-circle" style="object-fit:cover;">
    </div>
    
    <h5 class="profile-name">{{ $user->name }}</h5>
    <div class="profile-role">{{ $user->role ?? 'utilisateur' }}</div>
    
    @if(Auth::user()->role === 'patient')
      <a href="{{ route('patient.settings') }}" class="profile-settings-btn">
        <i class="bi bi-palette"></i>
        Personnaliser
      </a>
    @else
      <a href="{{ route('profile.edit') }}" class="profile-settings-btn">
        <i class="bi bi-gear"></i>
        Paramètres
      </a>
    @endif
    
    <hr class="profile-divider">
    
    <ul class="profile-info text-start">
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-envelope sidebar-icon"></i>Email</span>
          <span class="info-value">{{ Str::limit($user->email, 20) }}</span>
        </div>
      </li>
      
      @if(($user->role ?? '') === 'patient')
      @php $pp = $user->patient; @endphp
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-hash sidebar-icon"></i>N° dossier</span>
          <span class="info-value">{{ $pp->numero_dossier ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-telephone sidebar-icon"></i>Téléphone</span>
          <span class="info-value">{{ $pp->telephone ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-gender-ambiguous sidebar-icon"></i>Sexe</span>
          <span class="info-value">{{ $pp->sexe ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-calendar-event sidebar-icon"></i>Naissance</span>
          <span class="info-value">{{ optional($pp->date_naissance)->format('d/m/Y') ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-droplet sidebar-icon"></i>Groupe sang.</span>
          <span class="info-value">{{ $pp->groupe_sanguin ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label"><i class="bi bi-clipboard-pulse sidebar-icon"></i>Antécédents</span>
          <div class="info-value">{{ Str::limit($pp->antecedents ?? '—', 60) }}</div>
        </div>
      </li>
      
      @elseif(($user->role ?? '') === 'medecin')
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-stethoscope sidebar-icon"></i>Spécialité</span>
          <span class="info-value">{{ Str::limit($user->specialite ?? '—', 20) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-telephone sidebar-icon"></i>Tél. Pro</span>
          <span class="info-value">{{ $user->pro_phone ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-upc sidebar-icon"></i>Matricule</span>
          <span class="info-value">{{ $user->matricule ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-building sidebar-icon"></i>Cabinet</span>
          <span class="info-value">{{ Str::limit($user->cabinet ?? '—', 15) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label"><i class="bi bi-clock sidebar-icon"></i>Horaires consul.</span>
          <div class="info-value">{{ Str::limit($user->horaires ?? '—', 40) }}</div>
        </div>
      </li>
      @php
        $nursesCount = $user->nurses()->count();
        $nursesNames = $user->nurses()->limit(2)->pluck('name')->join(', ');
        $consultationsToday = $user->consultations()->whereDate('created_at', today())->count();
      @endphp
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label">
            <i class="bi bi-people sidebar-icon"></i>Infirmiers
            <span class="info-value-count {{ $nursesCount == 0 ? 'info-value-zero' : '' }}">{{ $nursesCount }}</span>
          </span>
          <div class="info-value team-members {{ !$nursesNames ? 'no-assignment' : '' }}">
            {{ $nursesNames ?: 'Aucun assigné' }}{{ $nursesCount > 2 ? '...' : '' }}
          </div>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-calendar-check sidebar-icon"></i>Consultations</span>
          <span class="info-value consultation-today">
            <span class="consultation-count {{ $consultationsToday == 0 ? 'zero' : '' }}">
              {{ $consultationsToday }}
            </span>
            aujourd'hui
          </span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-shield-check sidebar-icon"></i>Statut</span>
          <span class="info-value">
            <span class="status-badge {{ $user->active ? 'status-available' : 'status-unavailable' }}">
              {{ $user->active ? 'Disponible' : 'Indisponible' }}
            </span>
          </span>
        </div>
      </li>
      
      @elseif(($user->role ?? '') === 'secretaire')
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-telephone sidebar-icon"></i>Tél. Pro</span>
          <span class="info-value">{{ $user->pro_phone ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-upc sidebar-icon"></i>Matricule</span>
          <span class="info-value">{{ $user->matricule ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-briefcase sidebar-icon"></i>Service</span>
          <span class="info-value">{{ $user->specialite ?? 'Administration' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label"><i class="bi bi-clock sidebar-icon"></i>Horaires travail</span>
          <div class="info-value">{{ Str::limit($user->horaires ?? 'Lun-Ven 8h-17h', 40) }}</div>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-building sidebar-icon"></i>Bureau</span>
          <span class="info-value">{{ Str::limit($user->cabinet ?? 'Accueil', 15) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-shield-check sidebar-icon"></i>Statut</span>
          <span class="info-value">
            <span class="status-badge {{ $user->active ? 'status-active' : 'status-inactive' }}">
              {{ $user->active ? 'Actif' : 'Inactif' }}
            </span>
          </span>
        </div>
      </li>
      
      @elseif(($user->role ?? '') === 'infirmier')
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-telephone sidebar-icon"></i>Tél. Pro</span>
          <span class="info-value">{{ $user->pro_phone ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-upc sidebar-icon"></i>Matricule</span>
          <span class="info-value">{{ $user->matricule ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-heart-pulse sidebar-icon"></i>Spécialité</span>
          <span class="info-value">{{ Str::limit($user->specialite ?? 'Soins généraux', 20) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-hospital sidebar-icon"></i>Service</span>
          <span class="info-value">{{ Str::limit($user->cabinet ?? 'Urgences', 15) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label"><i class="bi bi-clock sidebar-icon"></i>Horaires garde</span>
          <div class="info-value">{{ Str::limit($user->horaires ?? 'Planning variable', 40) }}</div>
        </div>
      </li>
      @php
        $doctorsCount = $user->doctors()->count();
        $doctorsNames = $user->doctors()->limit(2)->pluck('name')->join(', ');
      @endphp
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label">
            <i class="bi bi-people sidebar-icon"></i>Médecins
            <span class="info-value-count {{ $doctorsCount == 0 ? 'info-value-zero' : '' }}">{{ $doctorsCount }}</span>
          </span>
          <div class="info-value team-members {{ !$doctorsNames ? 'no-assignment' : '' }}">
            {{ $doctorsNames ?: 'Aucun assigné' }}{{ $doctorsCount > 2 ? '...' : '' }}
          </div>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-shield-check sidebar-icon"></i>Statut</span>
          <span class="info-value">
            <span class="status-badge {{ $user->active ? 'status-active' : 'status-inactive' }}">
              {{ $user->active ? 'Actif' : 'Inactif' }}
            </span>
          </span>
        </div>
      </li>
      
      @elseif(($user->role ?? '') === 'admin')
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-telephone sidebar-icon"></i>Tél. Pro</span>
          <span class="info-value">{{ $user->pro_phone ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-upc sidebar-icon"></i>Matricule</span>
          <span class="info-value">{{ $user->matricule ?? '—' }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-shield-fill-exclamation sidebar-icon"></i>Niveau accès</span>
          <span class="info-value">
            <span class="status-badge status-admin">Administrateur</span>
          </span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-building sidebar-icon"></i>Département</span>
          <span class="info-value">{{ Str::limit($user->specialite ?? 'IT/Système', 20) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-geo-alt sidebar-icon"></i>Bureau</span>
          <span class="info-value">{{ Str::limit($user->cabinet ?? 'Administration', 15) }}</span>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label"><i class="bi bi-clock sidebar-icon"></i>Disponibilité</span>
          <div class="info-value">{{ Str::limit($user->horaires ?? '24/7 Support', 40) }}</div>
        </div>
      </li>
      @php
        $totalUsers = \App\Models\User::where('role', '!=', 'admin')->count();
        $activeUsers = \App\Models\User::where('role', '!=', 'admin')->where('active', true)->count();
      @endphp
      <li class="profile-info-item">
        <div class="info-block">
          <span class="info-label">
            <i class="bi bi-people sidebar-icon"></i>Utilisateurs
            <span class="info-value-count">{{ $totalUsers }}</span>
          </span>
          <div class="info-value">
            <span class="info-value-highlight">{{ $activeUsers }} actifs</span> / {{ $totalUsers }} total
          </div>
        </div>
      </li>
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-shield-check sidebar-icon"></i>Statut</span>
          <span class="info-value">
            <span class="status-badge {{ $user->active ? 'status-active' : 'status-inactive' }}">
              {{ $user->active ? 'Actif' : 'Inactif' }}
            </span>
          </span>
        </div>
      </li>
      @endif
    </ul>
  </div>
</div>
