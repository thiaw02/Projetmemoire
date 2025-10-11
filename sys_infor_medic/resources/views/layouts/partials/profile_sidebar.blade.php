@php
  $user = auth()->user();
  $rawAvatar = $user->avatar_url ?: ('https://ui-avatars.com/api/?size=120&name=' . urlencode($user->name));
  $avatar = (str_starts_with($rawAvatar, 'http://') || str_starts_with($rawAvatar, 'https://') || str_starts_with($rawAvatar, '//'))
            ? $rawAvatar
            : asset(ltrim($rawAvatar, '/'));
@endphp

<style>
  .modern-sidebar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 24px;
    color: white;
    border: none;
    box-shadow: 0 10px 40px rgba(102, 126, 234, 0.2);
    overflow: hidden;
    position: relative;
  }
  
  .modern-sidebar::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: repeating-linear-gradient(
      45deg,
      rgba(255, 255, 255, 0.05),
      rgba(255, 255, 255, 0.05) 1px,
      transparent 1px,
      transparent 10px
    );
    opacity: 0.3;
  }
  
  .sidebar-body {
    padding: 2rem 1.5rem;
    position: relative;
    z-index: 1;
  }
  
  .profile-avatar {
    position: relative;
    margin-bottom: 1.5rem;
  }
  
  .profile-avatar img {
    width: 90px;
    height: 90px;
    border: 4px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
  }
  
  .profile-avatar:hover img {
    transform: scale(1.05);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.25);
  }
  
  .profile-name {
    font-size: 1.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: white;
  }
  
  .profile-role {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
    margin-bottom: 1.5rem;
  }
  
  .profile-settings-btn {
    background: rgba(255, 255, 255, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.6rem 1.5rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }
  
  .profile-settings-btn:hover {
    background: white;
    color: #667eea;
    border-color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
  }
  
  .profile-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    margin: 1.5rem 0;
    border: none;
  }
  
  .profile-info {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .profile-info-item {
    background: rgba(255, 255, 255, 0.08);
    border-radius: 12px;
    padding: 0.8rem 1rem;
    margin-bottom: 0.75rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
  }
  
  .profile-info-item:hover {
    background: rgba(255, 255, 255, 0.12);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(5px);
  }
  
  .profile-info-item:last-child {
    margin-bottom: 0;
  }
  
  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .info-label {
    font-size: 0.8rem;
    font-weight: 500;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .info-value {
    font-size: 0.8rem;
    font-weight: 600;
    opacity: 0.95;
  }
  
  .info-block {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
  
  .info-block .info-label {
    opacity: 0.7;
    font-size: 0.75rem;
  }
  
  .info-block .info-value {
    font-size: 0.85rem;
    line-height: 1.4;
  }
  
  .sidebar-icon {
    width: 16px;
    height: 16px;
    opacity: 0.8;
  }
</style>

<div class="modern-sidebar">
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
          <span class="info-label"><i class="bi bi-briefcase sidebar-icon"></i>Spécialité</span>
          <span class="info-value">{{ Str::limit($user->specialite ?? '—', 20) }}</span>
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
          <span class="info-label"><i class="bi bi-clock sidebar-icon"></i>Horaires</span>
          <div class="info-value">{{ Str::limit($user->horaires ?? '—', 50) }}</div>
        </div>
      </li>
      
      @elseif(in_array(($user->role ?? ''), ['secretaire','infirmier']))
      <li class="profile-info-item">
        <div class="info-row">
          <span class="info-label"><i class="bi bi-telephone sidebar-icon"></i>Tél. Pro</span>
          <span class="info-value">{{ $user->pro_phone ?? '—' }}</span>
        </div>
      </li>
      @endif
    </ul>
  </div>
</div>
