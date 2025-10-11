@extends('layouts.app')

@section('content')
<style>
  /* Chat WhatsApp intégré avec sidebar */
  .chat-container {
    background: #f0f2f5;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(39, 174, 96, 0.1);
  }
  
  .chat-wrapper {
    display: flex;
    height: 80vh;
    min-height: 600px;
    background: #fff;
  }
  
  /* Sidebar des conversations */
  .chat-sidebar {
    width: 320px;
    background: #fff;
    border-right: 1px solid #e4e6ea;
    display: flex;
    flex-direction: column;
    min-width: 280px;
  }
  
  .sidebar-header {
    padding: 12px 16px;
    background: #27ae60;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .search-container {
    padding: 10px 15px;
    background: #f6f6f6;
    border-bottom: 1px solid #e4e6ea;
  }
  
  .search-box {
    width: 100%;
    padding: 10px 40px 10px 15px;
    border: none;
    border-radius: 20px;
    background: #fff;
    font-size: 14px;
  }
  
  .search-icon {
    position: absolute;
    right: 25px;
    top: 50%;
    transform: translateY(-50%);
    color: #54656f;
  }
  
  /* Liste des conversations */
  .conversations-list {
    flex: 1;
    overflow-y: auto;
    background: #fff;
  }
  
  .conversation-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f0f2f5;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
  }
  
  .conversation-item:hover {
    background: #f5f6f6;
  }
  
  .conversation-item.active {
    background: #e6f4ea;
    border-right: 3px solid #27ae60;
  }
  
  .conversation-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #27ae60;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 15px;
    font-size: 18px;
  }
  
  .conversation-content {
    flex: 1;
    min-width: 0;
  }
  
  .conversation-name {
    font-weight: 500;
    color: #111b21;
    margin-bottom: 2px;
    display: flex;
    justify-content: between;
    align-items: center;
  }
  
  .conversation-role {
    font-size: 11px;
    background: #e3f2fd;
    color: #1565c0;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
  }
  
  .conversation-last {
    font-size: 13px;
    color: #667781;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .conversation-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-left: 10px;
  }
  
  .conversation-time {
    font-size: 12px;
    color: #667781;
    margin-bottom: 4px;
  }
  
  .unread-badge {
    background: #27ae60;
    color: white;
    border-radius: 50%;
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 600;
  }
  
  /* Zone de chat principale */
  .chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #efeae2;
  }
  
  .chat-header {
    padding: 15px 20px;
    background: #f0f2f5;
    border-bottom: 1px solid #d1d7db;
    display: flex;
    align-items: center;
  }
  
  .chat-header-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #27ae60;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    margin-right: 15px;
  }
  
  .chat-header-info h5 {
    margin: 0;
    color: #111b21;
    font-size: 16px;
  }
  
  .chat-header-status {
    font-size: 13px;
    color: #667781;
  }
  
  /* Zone des messages */
  .chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: repeating-linear-gradient(
      45deg,
      rgba(145, 145, 145, 0.02),
      rgba(145, 145, 145, 0.02) 1px,
      transparent 1px,
      transparent 20px
    );
  }
  
  /* Messages bubbles */
  .message {
    margin-bottom: 12px;
    display: flex;
    align-items: flex-end;
  }
  
  .message.sent {
    justify-content: flex-end;
  }
  
  .message-bubble {
    max-width: 65%;
    padding: 8px 12px;
    border-radius: 8px;
    position: relative;
    word-wrap: break-word;
  }
  
  .message.sent .message-bubble {
    background: #d9fdd3;
    color: #111b21;
    border-bottom-right-radius: 2px;
  }
  
  .message.received .message-bubble {
    background: #ffffff;
    color: #111b21;
    border-bottom-left-radius: 2px;
  }
  
  .message-content {
    margin-bottom: 4px;
    line-height: 1.4;
  }
  
  .message-time {
    font-size: 11px;
    color: #667781;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 4px;
  }
  
  .message.sent .message-time {
    color: #27ae60;
  }
  
  .read-status {
    margin-left: 4px;
    font-size: 16px;
    color: #27ae60;
  }
  
  /* Zone de saisie */
  .chat-input-container {
    padding: 15px 20px;
    background: #f0f2f5;
    border-top: 1px solid #d1d7db;
  }
  
  .chat-input-wrapper {
    display: flex;
    align-items: flex-end;
    background: #ffffff;
    border-radius: 25px;
    padding: 8px;
  }
  
  .chat-input {
    flex: 1;
    border: none;
    outline: none;
    padding: 8px 15px;
    font-size: 14px;
    resize: none;
    max-height: 80px;
  }
  
  .chat-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-right: 8px;
  }
  
  .action-btn {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: #27ae60;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.2s;
  }
  
  .action-btn:hover {
    background: #1e8449;
  }
  
  .action-btn.secondary {
    background: #54656f;
  }
  
  .action-btn.secondary:hover {
    background: #3b4a54;
  }
  
  .action-btn.recording {
    background: #e53e3e;
    animation: pulse 1.5s infinite;
  }
  
  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }
  
  /* Responsive */
  @media (max-width: 768px) {
    .chat-wrapper {
      height: 70vh;
      flex-direction: column;
    }
    
    .chat-sidebar {
      width: 100%;
      height: 200px;
      min-width: unset;
      border-right: none;
      border-bottom: 1px solid #e4e6ea;
    }
    
    .chat-main {
      flex: 1;
    }
    
    .conversations-list {
      height: 150px;
    }
  }
  
  /* Scrollbar personnalisé */
  .conversations-list::-webkit-scrollbar,
  .chat-messages::-webkit-scrollbar {
    width: 6px;
  }
  
  .conversations-list::-webkit-scrollbar-thumb,
  .chat-messages::-webkit-scrollbar-thumb {
    background: #0000001a;
    border-radius: 3px;
  }
  
  /* Typing indicator */
  .typing-indicator {
    display: flex;
    align-items: center;
    color: #27ae60;
    font-size: 13px;
    font-style: italic;
  }
  
  .typing-dots {
    margin-left: 8px;
    display: flex;
    gap: 2px;
  }
  
  .typing-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #27ae60;
    animation: typing 1.4s infinite ease-in-out;
  }
  
  .typing-dot:nth-child(1) { animation-delay: -0.32s; }
  .typing-dot:nth-child(2) { animation-delay: -0.16s; }
  
  @keyframes typing {
    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
  }
  
  /* File attachments */
  .message-file {
    background: rgba(0,0,0,0.05);
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 8px;
  }
  
  .message-image {
    max-width: 100%;
    border-radius: 8px;
    margin-bottom: 8px;
  }
  
  .message-audio {
    width: 100%;
    margin-bottom: 8px;
  }
  
  .no-partner {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #667781;
    text-align: center;
  }
  
  .no-partner i {
    font-size: 64px;
    margin-bottom: 20px;
    color: #d1d7db;
  }
</style>
@php
  $role = auth()->user()->role ?? null;
  $dash = $role==='admin' ? route('admin.dashboard') : ($role==='secretaire' ? route('secretaire.dashboard') : ($role==='medecin' ? route('medecin.dashboard') : ($role==='infirmier' ? route('infirmier.dashboard') : ($role==='patient' ? route('patient.dashboard') : route('login')))));
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="text-success mb-0"><i class="bi bi-chat-dots me-2"></i>Discussions</h3>
  <a href="{{ $dash }}" class="btn btn-outline-primary btn-sm">
    <i class="bi bi-arrow-left me-1"></i>Retour
  </a>
</div>

<div class="chat-container">
  <div class="chat-wrapper">
    <!-- Sidebar des conversations -->
    <div class="chat-sidebar">
      <div class="sidebar-header">
        <div class="d-flex align-items-center">
          <div class="conversation-avatar" style="width: 32px; height: 32px; font-size: 14px; margin-right: 10px;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
          </div>
          <div>
            <h6 class="mb-0">{{ auth()->user()->name }}</h6>
            <small class="opacity-75">{{ ucfirst(auth()->user()->role) }}</small>
          </div>
        </div>
      </div>
      
      <div class="search-container">
        <div class="position-relative">
          <input type="text" class="search-box" placeholder="Rechercher une conversation..." id="searchBox">
          <i class="bi bi-search search-icon"></i>
        </div>
      </div>
      
      <div class="conversations-list" id="conversationsList">
        @php $uid = auth()->id(); @endphp
        @forelse($partners as $p)
        @php
          $unread = \App\Models\Message::whereNull('read_at')
            ->where('sender_id','!=',$uid)
            ->whereHas('conversation', function($q) use ($uid, $p){
              $q->where(function($qq) use ($uid,$p){ $qq->where('user_one_id',$uid)->where('user_two_id',$p->id); })
                ->orWhere(function($qq) use ($uid,$p){ $qq->where('user_one_id',$p->id)->where('user_two_id',$uid); });
            })->count();
          
          // Dernier message pour aperçu
          $lastMessage = \App\Models\Message::whereHas('conversation', function($q) use ($uid, $p){
            $q->where(function($qq) use ($uid,$p){ $qq->where('user_one_id',$uid)->where('user_two_id',$p->id); })
              ->orWhere(function($qq) use ($uid,$p){ $qq->where('user_one_id',$p->id)->where('user_two_id',$uid); });
          })->latest()->first();
        @endphp
        
        <a href="{{ route('chat.index', ['partner_id'=>$p->id]) }}" 
           class="conversation-item {{ ($partner && $partner->id===$p->id) ? 'active' : '' }}" 
           data-name="{{ strtolower($p->name) }}" 
           data-role="{{ strtolower($p->role) }}">
          <div class="conversation-avatar">
            {{ strtoupper(substr($p->name, 0, 1)) }}
          </div>
          <div class="conversation-content">
            <div class="conversation-name">
              <span>{{ $p->name }}</span>
              <span class="conversation-role">{{ ucfirst($p->role) }}</span>
            </div>
            <div class="conversation-last">
              @if($lastMessage)
                @if($lastMessage->file_path && !$lastMessage->body)
                  <i class="bi bi-paperclip me-1"></i>
                  @if($lastMessage->file_type === 'image')
                    Image
                  @elseif($lastMessage->file_type === 'audio')
                    Message vocal
                  @else
                    Fichier
                  @endif
                @else
                  {{ Str::limit($lastMessage->body ?: 'Fichier joint', 30) }}
                @endif
              @else
                Commencer une conversation...
              @endif
            </div>
          </div>
          <div class="conversation-meta">
            @if($lastMessage)
              <div class="conversation-time">
                {{ $lastMessage->created_at->diffForHumans(null, true) }}
              </div>
            @endif
            @if($unread > 0)
              <div class="unread-badge">{{ $unread }}</div>
            @endif
          </div>
        </a>
        @empty
        <div class="p-4 text-center text-muted">
          <i class="bi bi-chat-square-text mb-3" style="font-size: 2rem;"></i>
          <p>Aucune conversation disponible</p>
        </div>
        @endforelse
      </div>
    </div>
    
    <!-- Zone de chat principale -->
    <div class="chat-main">
      @if($partner)
        <!-- Header du chat -->
        <div class="chat-header">
          <div class="chat-header-avatar">
            {{ strtoupper(substr($partner->name, 0, 1)) }}
          </div>
          <div class="chat-header-info">
            <h5>{{ $partner->name }}</h5>
            <div class="chat-header-status">
              <span class="badge bg-secondary">{{ ucfirst($partner->role) }}</span>
              <span id="typingIndicator" class="typing-indicator ms-2" style="display: none;">
                En train d'écrire
                <div class="typing-dots">
                  <div class="typing-dot"></div>
                  <div class="typing-dot"></div>
                  <div class="typing-dot"></div>
                </div>
              </span>
            </div>
          </div>
        </div>
        
        <!-- Zone des messages -->
        <div class="chat-messages" id="chatMessages">
          @forelse($messages as $m)
          <div class="message {{ $m->sender_id === $user->id ? 'sent' : 'received' }}">
            <div class="message-bubble">
              @if(!empty($m->file_path))
                @if($m->file_type==='image')
                  <div class="message-image">
                    <img src="{{ $m->file_path }}" alt="Image" class="message-image" style="max-width: 250px;">
                  </div>
                @elseif($m->file_type==='audio')
                  <div class="message-audio">
                    <audio controls class="message-audio">
                      <source src="{{ $m->file_path }}" type="audio/mpeg">
                      Votre navigateur ne supporte pas l'audio.
                    </audio>
                  </div>
                @else
                  <div class="message-file">
                    <i class="bi bi-file-earmark me-2"></i>
                    <a href="{{ $m->file_path }}" target="_blank" class="text-decoration-none">
                      Télécharger le fichier
                    </a>
                  </div>
                @endif
              @endif
              
              @if(!empty($m->body))
                <div class="message-content">{{ $m->body }}</div>
              @endif
              
              <div class="message-time">
                {{ $m->created_at->format('H:i') }}
                @if($m->sender_id === $user->id)
                  @if($m->read_at)
                    <i class="bi bi-check2-all read-status" title="Lu"></i>
                  @else
                    <i class="bi bi-check2 text-muted" title="Envoyé"></i>
                  @endif
                @endif
              </div>
            </div>
          </div>
          @empty
          <div class="d-flex align-items-center justify-content-center h-100">
            <div class="text-center text-muted">
              <i class="bi bi-chat-dots mb-3" style="font-size: 3rem;"></i>
              <h5>Commencez la conversation</h5>
              <p>Envoyez votre premier message à {{ $partner->name }}</p>
            </div>
          </div>
          @endforelse
        </div>
        
        <!-- Zone de saisie -->
        <div class="chat-input-container">
          <form method="POST" action="{{ route('chat.send') }}" enctype="multipart/form-data" id="sendForm">
            @csrf
            <input type="hidden" name="partner_id" value="{{ $partner->id }}">
            <div class="chat-input-wrapper">
              <div class="chat-actions">
                <label class="action-btn secondary" title="Joindre un fichier">
                  <i class="bi bi-paperclip"></i>
                  <input type="file" name="file" id="fileInput" style="display: none;" accept=".pdf,image/*,audio/*">
                </label>
              </div>
              
              <textarea name="body" class="chat-input" placeholder="Tapez votre message..." rows="1" id="messageInput"></textarea>
              
              <div class="chat-actions">
                <button type="button" id="btnRecord" class="action-btn secondary" title="Message vocal">
                  <i class="bi bi-mic-fill"></i>
                </button>
                <button type="submit" class="action-btn" title="Envoyer">
                  <i class="bi bi-send-fill"></i>
                </button>
              </div>
            </div>
            <div id="recStatus" class="text-center text-danger mt-2" style="display:none;">
              <small><i class="bi bi-record-circle me-1"></i>Enregistrement en cours...</small>
            </div>
          </form>
        </div>
        
      @else
        <div class="no-partner">
          <i class="bi bi-chat-square-text"></i>
          <h4>Sélectionnez une conversation</h4>
          <p>Choisissez un contact dans la liste pour commencer à discuter</p>
        </div>
      @endif
    </div>
  </div>
</div>
<script>
  (function(){
    const partnerId = {{ $partner?->id ?? 'null' }};
    const box = document.getElementById('chatMessages');
    const fileInput = document.getElementById('fileInput');
    const recBtn = document.getElementById('btnRecord');
    const recStatus = document.getElementById('recStatus');
    const messageInput = document.getElementById('messageInput');
    const searchBox = document.getElementById('searchBox');
    const conversationsList = document.getElementById('conversationsList');
    
    let mediaRecorder = null; let recChunks = []; let stream = null;
    const typing = document.getElementById('typingIndicator');
    let typingTimeout = null;
    
    // Fonction pour faire défiler vers le bas
    function scrollBottom(){ 
      if (box) { 
        setTimeout(() => {
          box.scrollTop = box.scrollHeight; 
        }, 100);
      } 
    }
    scrollBottom();
    
    // Auto-resize du textarea
    if (messageInput) {
      messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
        
        // Typing indicator
        if(typingTimeout) clearTimeout(typingTimeout);
        sendTyping();
        typingTimeout = setTimeout(()=>{}, 2000);
      });
      
      // Envoyer avec Enter
      messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
          e.preventDefault();
          document.getElementById('sendForm').requestSubmit();
        }
      });
    }
    
    // Recherche dans les conversations
    if (searchBox && conversationsList) {
      searchBox.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const conversations = conversationsList.querySelectorAll('.conversation-item');
        
        conversations.forEach(conv => {
          const name = conv.getAttribute('data-name') || '';
          const role = conv.getAttribute('data-role') || '';
          const isVisible = name.includes(query) || role.includes(query);
          conv.style.display = isVisible ? 'flex' : 'none';
        });
      });
    }
    
    // Rafraîchir les messages
    async function refreshMessages(){
      if(!partnerId) return;
      try{
        const r = await fetch(`{{ route('chat.messages') }}?partner_id=${partnerId}`, { 
          headers: { 'Accept':'application/json' } 
        });
        if(!r.ok) return;
        const data = await r.json();
        if(!Array.isArray(data.messages)) return;
        
        let html='';
        for(const m of data.messages){
          const messageClass = m.mine ? 'sent' : 'received';
          html += `<div class="message ${messageClass}">`;
          html += `<div class="message-bubble">`;
          
          // Fichiers joints
          if (m.file_path) {
            if (m.file_type === 'image') {
              html += `<div class="message-image">`;
              html += `<img src="${m.file_path}" alt="Image" style="max-width: 250px; border-radius: 8px;">`;
              html += `</div>`;
            } else if (m.file_type === 'audio') {
              html += `<div class="message-audio">`;
              html += `<audio controls><source src="${m.file_path}" type="audio/mpeg"></audio>`;
              html += `</div>`;
            } else {
              html += `<div class="message-file">`;
              html += `<i class="bi bi-file-earmark me-2"></i>`;
              html += `<a href="${m.file_path}" target="_blank">Télécharger le fichier</a>`;
              html += `</div>`;
            }
          }
          
          // Contenu du message
          if (m.body) {
            html += `<div class="message-content">${m.body}</div>`;
          }
          
          // Heure et statut
          html += `<div class="message-time">`;
          html += new Date(m.created_at).toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});
          if (m.mine) {
            if (m.read) {
              html += `<i class="bi bi-check2-all read-status ms-1" title="Lu"></i>`;
            } else {
              html += `<i class="bi bi-check2 text-muted ms-1" title="Envoyé"></i>`;
            }
          }
          html += `</div>`;
          html += `</div></div>`;
        }
        
        box.innerHTML = html;
        scrollBottom();
      }catch(e){ console.warn('Erreur refresh messages:', e); }
    }
    
    // Rafraîchir l'indicateur de frappe
    async function refreshTyping(){
      if(!partnerId || !typing) return;
      try{
        const r = await fetch(`{{ route('chat.typingStatus') }}?partner_id=${partnerId}`, { 
          headers: { 'Accept':'application/json' } 
        });
        if(!r.ok) return;
        const data = await r.json();
        
        if (data.typing) {
          typing.style.display = 'inline-flex';
        } else {
          typing.style.display = 'none';
        }
      }catch(e){ /* noop */ }
    }
    
    // Envoyer signal de frappe
    async function sendTyping(){
      if(!partnerId) return;
      try{
        await fetch(`{{ route('chat.typing') }}`, {
          method: 'POST',
          headers: { 
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({ partner_id: partnerId })
        });
      }catch(e){ /* noop */ }
    }
    
    // Intervals de rafraîchissement
    setInterval(refreshMessages, 8000);
    setInterval(refreshTyping, 3000);

    // Enregistrement vocal
    async function startRecording(){
      try{
        if(!partnerId){ 
          alert('Sélectionnez un contact pour envoyer un message vocal.'); 
          return; 
        }
        
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        recChunks = [];
        mediaRecorder = new MediaRecorder(stream);
        
        mediaRecorder.ondataavailable = e => { 
          if(e.data.size > 0) recChunks.push(e.data); 
        };
        
        mediaRecorder.onstop = () => {
          const blob = new Blob(recChunks, { type: 'audio/webm' });
          const file = new File([blob], `voice_${Date.now()}.webm`, { type: 'audio/webm' });
          const dt = new DataTransfer(); 
          dt.items.add(file); 
          fileInput.files = dt.files;
          
          // Reset UI
          recStatus.style.display = 'none';
          recBtn.classList.remove('recording');
          recBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
          
          // Stop stream
          try{ 
            stream.getTracks().forEach(t => t.stop()); 
          } catch(_){ }
          
          // Auto-send comme WhatsApp
          const form = document.getElementById('sendForm');
          try{ 
            form?.requestSubmit(); 
          } catch(_){ 
            form?.submit(); 
          }
        };
        
        mediaRecorder.start();
        recStatus.style.display = 'block';
        recBtn.classList.add('recording');
        recBtn.innerHTML = '<i class="bi bi-stop-fill"></i>';
        
      } catch(e) { 
        console.warn('Erreur enregistrement audio:', e);
        alert('Impossible d\'accéder au microphone. Vérifiez les autorisations.');
      }
    }
    
    function stopRecording(){ 
      if(mediaRecorder && mediaRecorder.state !== 'inactive'){ 
        mediaRecorder.stop(); 
      } 
    }
    
    // Event listener pour l'enregistrement
    recBtn?.addEventListener('click', () => {
      if(!mediaRecorder || mediaRecorder.state === 'inactive'){ 
        startRecording(); 
      } else { 
        stopRecording(); 
      }
    });
    
    // Focus automatique sur l'input de message
    if (messageInput && partnerId) {
      messageInput.focus();
    }
    
  })();
</script>
@endsection
