<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SMART-HEALTH</title>

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Police principale */
        body {
            background-color: #f0f9f4;
            font-family: 'Roboto', sans-serif;
            color: #2c3e50;
            min-height: 100vh;
            margin: 0;
            padding-top: 80px;
            padding-bottom: 40px;
        }
        
        /* Pages sans navbar */
        body.no-navbar {
            padding-top: 0;
        }

        /* Navbar */
        nav.navbar {
            background-color: #e6f4ea !important;
            box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            transition: all 0.3s ease;
        }

        nav.navbar .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #27ae60 !important;
            text-transform: uppercase;
        }

        nav.navbar .navbar-brand img {
            filter: drop-shadow(0 0 1px rgba(39, 174, 96, 0.7));
        }

        /* Container général */
        .container {
            max-width: 1100px;
        }

        /* Cartes */
        .card {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgb(39 174 96 / 0.1);
            transition: transform 0.15s ease-in-out;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 30px rgb(39 174 96 / 0.2);
        }

        .card-header {
            background-color: #c2e9c9 !important;
            font-weight: 700;
            font-size: 1.2rem;
            color: #145a32;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        /* Tabs */
        .nav-tabs .nav-link {
            color: #27ae60;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            border-bottom: 2px solid transparent;
            transition: border-color 0.3s;
        }

        .nav-tabs .nav-link.active {
            border-color: #27ae60;
            color: #145a32;
        }

        .nav-tabs .nav-link:hover:not(.active) {
            color: #52b788;
        }

        /* Boutons */
        .btn-success {
            background-color: #27ae60;
            border: none;
        }

        .btn-success:hover {
            background-color: #1e8449;
        }

        .btn-primary {
            background-color: #2d7a2b;
            border: none;
        }

        .btn-primary:hover {
            background-color: #23641f;
        }

        .btn-danger {
            background-color: #c0392b;
            border: none;
        }

        .btn-danger:hover {
            background-color: #922b21;
        }

        /* Table */
        table.table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgb(0 0 0 / 0.05);
        }

        table.table thead {
            background-color: #a3d9a5;
            color: #145a32;
        }

        /* Boutons actions */
        button.btn-sm {
            min-width: 70px;
        }

        /* ========== SYSTEME DE NOTIFICATIONS MODERNE ========== */
        
        /* Bouton de notifications moderne */
        .modern-notification-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(39, 174, 96, 0.2);
            background: white;
            color: #27ae60;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.1);
        }
        
        .modern-notification-btn:hover {
            background: #27ae60;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .modern-notification-btn i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }
        
        .modern-notification-btn:hover i {
            transform: scale(1.1);
        }
        
        /* Badge de notifications */
        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
            animation: bounce 0.6s ease-in-out;
        }
        
        /* Animation de pulsation pour les nouvelles notifications */
        .notification-pulse {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: rgba(39, 174, 96, 0.3);
            transform: translate(-50%, -50%);
            animation: pulse 2s infinite;
            opacity: 0;
        }
        
        .modern-notification-btn.has-notifications .notification-pulse {
            opacity: 1;
        }
        
        /* Panel de notifications moderne */
        .modern-notification-panel {
            position: absolute;
            top: calc(100% + 15px);
            right: 0;
            width: 380px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(39, 174, 96, 0.1);
            overflow: hidden;
            z-index: 2000;
            transform: translateY(-10px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            max-height: 500px;
        }
        
        .modern-notification-panel:not(.d-none) {
            transform: translateY(0);
            opacity: 1;
        }
        
        /* En-tête du panel */
        .notification-header {
            background: linear-gradient(135deg, #27ae60, #52b788);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .notification-header h6 {
            font-weight: 600;
            color: white;
        }
        
        .mark-all-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            border-radius: 8px;
            padding: 0.4rem 0.6rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .mark-all-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }
        
        /* Contenu des notifications */
        .notification-content {
            max-height: 350px;
            overflow-y: auto;
        }
        
        .notification-section {
            border-bottom: 1px solid #f0f0f0;
        }
        
        .notification-section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            padding: 0.8rem 1.5rem 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8f9fa;
        }
        
        .section-count {
            background: #27ae60;
            color: white;
            border-radius: 10px;
            padding: 0.2rem 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
        }
        
        /* Liste des notifications */
        .notification-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .notification-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f5f5f5;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }
        
        .notification-item:hover {
            background: #f8f9fa;
        }
        
        .notification-item.unread {
            background: rgba(39, 174, 96, 0.03);
            border-left: 3px solid #27ae60;
        }
        
        .notification-item.unread::after {
            content: '';
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 8px;
            background: #27ae60;
            border-radius: 50%;
        }
        
        /* Pied du panel */
        .notification-footer {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        
        .view-all-link {
            color: #27ae60;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }
        
        .view-all-link:hover {
            color: #1e8449;
        }
        
        /* ========== SYSTEME DE TOASTS MODERNE ========== */
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        
        .modern-toast {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-left: 4px solid #27ae60;
            margin-bottom: 1rem;
            overflow: hidden;
            transform: translateX(100%);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
        }
        
        .modern-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .modern-toast.success { border-left-color: #27ae60; }
        .modern-toast.error { border-left-color: #e74c3c; }
        .modern-toast.warning { border-left-color: #f39c12; }
        .modern-toast.info { border-left-color: #3498db; }
        
        .toast-header {
            background: #f8f9fa;
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .toast-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        
        .toast-icon.success { background: #27ae60; }
        .toast-icon.error { background: #e74c3c; }
        .toast-icon.warning { background: #f39c12; }
        .toast-icon.info { background: #3498db; }
        
        .toast-title {
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .toast-close {
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 0;
            font-size: 1.2rem;
            transition: color 0.2s ease;
        }
        
        .toast-close:hover {
            color: #333;
        }
        
        .toast-body {
            padding: 1rem;
            font-size: 0.9rem;
            color: #555;
            line-height: 1.4;
        }
        
        .toast-progress {
            height: 3px;
            background: rgba(39, 174, 96, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .toast-progress-bar {
            height: 100%;
            background: #27ae60;
            width: 100%;
            transition: transform 5s linear;
        }
        
        .modern-toast.success .toast-progress-bar { background: #27ae60; }
        .modern-toast.error .toast-progress-bar { background: #e74c3c; }
        .modern-toast.warning .toast-progress-bar { background: #f39c12; }
        .modern-toast.info .toast-progress-bar { background: #3498db; }
        
        /* Animations */
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translate3d(0, 0, 0); }
            40%, 43% { transform: translate3d(0, -8px, 0); }
            70% { transform: translate3d(0, -4px, 0); }
            90% { transform: translate3d(0, -2px, 0); }
        }
        
        @keyframes pulse {
            0% { transform: translate(-50%, -50%) scale(1); opacity: 0.3; }
            50% { transform: translate(-50%, -50%) scale(1.1); opacity: 0.1; }
            100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; }
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .modern-notification-panel {
                width: 320px;
                right: -20px;
            }
            
            .toast-container {
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }
    </style>
    <!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales-all.min.js'></script>

</head>
<body class="@yield('body_class') {{ request()->routeIs('login','register','password.request','password.reset') ? 'no-navbar' : '' }}">
    @stack('styles')

    {{-- Navbar --}}
    @unless (request()->routeIs('login','register','password.request','password.reset'))
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm">
        <div class="container">
            <a class="navbar-brand text-success d-flex align-items-center" href="#">
                <img src="{{ asset('images/LOGO PLATEFORME.png') }}" alt="Logo" width="40" height="40" class="me-2">
                SMART-HEALTH
            </a>
            @auth
            <div class="ms-auto d-flex align-items-center position-relative">
              <button id="alertsToggle" type="button" class="modern-notification-btn me-2 position-relative">
                <i class="bi bi-bell"></i>
                <span id="notif-badge" class="notification-badge d-none"></span>
                <div class="notification-pulse"></div>
              </button>
              <div id="alertsPanel" class="modern-notification-panel d-none">
                <div class="notification-header">
                  <h6 class="mb-0">
                    <i class="bi bi-bell me-2"></i>Notifications
                  </h6>
                  <button id="markAllRead" class="mark-all-btn">
                    <i class="bi bi-check2-all"></i>
                  </button>
                </div>
                <div class="notification-content">
                  <div class="notification-section">
                    <div class="section-title">
                      <i class="bi bi-chat-dots me-1"></i>Messages
                      <span class="section-count" id="messagesCount">0</span>
                    </div>
                    <ul id="alertsMessages" class="notification-list"></ul>
                  </div>
                  <div class="notification-section">
                    <div class="section-title">
                      <i class="bi bi-bell me-1"></i>Alertes
                      <span class="section-count" id="alertsCount">0</span>
                    </div>
                    <ul id="alertsNotifications" class="notification-list"></ul>
                  </div>
                </div>
                <div class="notification-footer">
                  <a href="#" class="view-all-link">Voir toutes les notifications</a>
                </div>
              </div>
              <form action="{{ route('logout') }}" method="POST" class="mb-0 ms-2">
                @csrf
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i> Déconnexion</button>
              </form>
            </div>
            @endauth
        </div>
    </nav>
    @endunless

    @auth
    @unless (request()->routeIs('chat.*') || request()->routeIs('login','register','password.request','password.reset'))
    <style>
      .fab-chat { position: fixed; right: 22px; bottom: 24px; z-index: 1050; }
      .fab-chat .btn { width: 56px; height: 56px; border-radius: 50%; box-shadow: 0 6px 18px rgba(0,0,0,.2); display: inline-flex; align-items: center; justify-content: center; }
    </style>
    <div class="fab-chat">
      <a href="{{ route('chat.index') }}" class="btn btn-success" aria-label="Ouvrir le chat">
        <i class="bi bi-chat-dots" style="font-size: 1.3rem;"></i>
      </a>
    </div>
    @endunless
    @endauth

    {{-- Contenu de la page --}}
    @auth
@if(!request()->routeIs('login','register','password.request','password.reset','admin.dashboard','admin.audit.*','secretaire.dashboard','medecin.dashboard','infirmier.dashboard','patient.dashboard','patient.payments.*','patient.settings.*','profile.*','chat.*'))
    <div class="container mt-4">
      <div class="row">
        <div class="col-lg-3 mb-4">
          @include('layouts.partials.profile_sidebar')
        </div>
        <div class="col-lg-9">
          @yield('content')
        </div>
      </div>
    </div>
    @else
    <div class="container mt-4">
        @yield('content')
    </div>
    @endif
    @else
    <div class="container mt-4">
        @yield('content')
    </div>
    @endauth

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      // Expose CSRF token for JS fetch usage
      window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      // Current user id for per-user local storage keys
      window.userId = "{{ auth()->id() }}";
      window.userRole = "{{ auth()->user()->role ?? '' }}";
    </script>

    {{-- Zone pour scripts spécifiques aux vues --}}
    @yield('scripts')

    <script>
      // Alerts: dropdown + poll count
      (function(){
        const badge = document.getElementById('notif-badge');
        const toggle = document.getElementById('alertsToggle');
        const panel = document.getElementById('alertsPanel');
        const listMsgs = document.getElementById('alertsMessages');
        const listNotifs = document.getElementById('alertsNotifications');
        const uid = (window.userId||'').toString();
        const SEEN_KEY = `alerts_seen_count_${uid}`;
        async function fetchSummary(){
          const r = await fetch('{{ route('alerts.summary') }}', { headers: { 'Accept':'application/json' } });
          if(!r.ok) return null;
          return await r.json();
        }
        async function refreshBadge(){
          try{
            const data = await fetchSummary();
            if(!data) return;
            const c = parseInt(data.total_count||0);
            const seen = parseInt(localStorage.getItem(SEEN_KEY)||'0');
            if (c>seen){ badge.classList.remove('d-none'); badge.textContent = c - seen; }
            else { badge.classList.add('d-none'); badge.textContent = ''; }
          }catch(e){}
        }
        async function renderPanel(){
          const data = await fetchSummary();
          if(!data) return;
          try{ localStorage.setItem(SEEN_KEY, String(parseInt(data.total_count||0))); }catch(e){}
          badge?.classList.add('d-none');
          // Messages
          listMsgs.innerHTML = '';
          (data.messages||[]).slice(0,8).forEach(m=>{
            const badgeHtml = m.unread>0 ? `<span class="badge bg-success ms-1">${m.unread}</span>` : '';
            const li = document.createElement('li');
            li.className = 'py-1 border-bottom';
            li.innerHTML = `<div class="d-flex justify-content-between"><strong>${m.partner_name}</strong>${badgeHtml}</div>
                            <div class="small text-muted">${(m.last_at||'').replace('T',' ').slice(0,16)}</div>
                            <div class="small">${m.preview||''}</div>`;
            listMsgs.appendChild(li);
          });
          if (!listMsgs.children.length){ const li=document.createElement('li'); li.className='small text-muted px-1'; li.textContent='Aucun message.'; listMsgs.appendChild(li); }

          // Notifications
          listNotifs.innerHTML = '';
          if ((data.rdvRequests||[]).length){
            const header = document.createElement('li'); header.className='small text-success px-1 d-flex justify-content-between align-items-center'; header.innerHTML='<span>Demandes de rendez‑vous</span>'+(window.userRole==='secretaire'?`<a href=\"{{ route('secretaire.rendezvous') }}\" class=\"small\">Tout voir</a>`:''); listNotifs.appendChild(header);
            data.rdvRequests.forEach(it=>{
              const li = document.createElement('li');
              li.className = 'py-1 border-bottom';
              li.innerHTML = `<div><strong>${it.patient}</strong> → ${it.medecin||'—'}</div>
                              <div class=\"small text-muted\">${it.date} ${it.heure}</div>`;
              listNotifs.appendChild(li);
            });
          }
          if ((data.rdvUpdates||[]).length){
            const header = document.createElement('li'); header.className='small text-success px-1 mt-1'; header.textContent='Statuts de vos rendez‑vous'; listNotifs.appendChild(header);
            data.rdvUpdates.forEach(it=>{
              const li = document.createElement('li');
              li.className = 'py-1 border-bottom';
              const cls = (/confirm/i.test(it.statut)?'bg-success':(/annul/i.test(it.statut)?'bg-secondary':'bg-warning text-dark'));
              li.innerHTML = `<div><span class="badge ${cls}">${(it.statut||'').replace('_',' ')}</span> avec ${it.medecin||'—'}</div>
                              <div class="small text-muted">${it.date} ${it.heure}</div>`;
              listNotifs.appendChild(li);
            });
          }
          if ((data.ordonnances||[]).length){
            const header = document.createElement('li'); header.className='small text-success px-1 mt-1'; header.textContent='Ordonnances récentes'; listNotifs.appendChild(header);
            data.ordonnances.forEach(it=>{
              const li = document.createElement('li');
              li.className = 'py-1 border-bottom';
              li.innerHTML = `<div>Nouvelle ordonnance disponible</div>
                              <div class="small text-muted">${(it.created_at||'').replace('T',' ').slice(0,16)}</div>`;
              listNotifs.appendChild(li);
            });
          }
          if (!listNotifs.children.length){ const li=document.createElement('li'); li.className='small text-muted px-1'; li.textContent='Aucune notification.'; listNotifs.appendChild(li); }
        }
        let open=false;
        let intervalId = null;
        function startPolling(){ if(intervalId) return; intervalId = setInterval(refreshBadge, 15000); }
        function stopPolling(){ if(intervalId){ clearInterval(intervalId); intervalId=null; } }
        toggle?.addEventListener('click', async ()=>{
          open = !open;
          if(open){ await renderPanel(); panel?.classList.remove('d-none'); panel?.classList.add('slide-in'); badge?.classList.add('d-none'); stopPolling(); }
          else { panel?.classList.add('d-none'); startPolling(); }
        });
        document.addEventListener('click', (e)=>{ if(!panel || !toggle) return; if (!panel.contains(e.target) && !toggle.contains(e.target)){ panel.classList.add('d-none'); open=false; startPolling(); }});
        startPolling();
        refreshBadge();
      })();
    </script>

</body>
</html>
