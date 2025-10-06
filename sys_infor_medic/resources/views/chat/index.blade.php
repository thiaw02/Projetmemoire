@extends('layouts.app')

@section('content')
<style>
  .chat-wrapper { max-width: 980px; margin: 1rem auto; }
  .chat-box { background: #fff; border: 1px solid #e6f4ea; border-radius: .75rem; box-shadow: 0 6px 18px rgba(0,0,0,.06); }
  .chat-messages { height: 380px; overflow-y: auto; padding: 1rem; background: #f8fff9; }
  .chat-input { border-top: 1px solid #e6f4ea; padding: .75rem; }
  .msg { margin-bottom: .5rem; }
  .msg.me { text-align: right; }
  .msg .bubble { display: inline-block; padding: .5rem .75rem; border-radius: .75rem; max-width: 70%; }
  .msg.me .bubble { background: #27ae60; color: #fff; }
  .msg.other .bubble { background: #eafaf0; color: #145a32; }
  .rec-indicator { color: #e53935; font-weight: 600; }
</style>
<div class="container chat-wrapper">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-success mb-0">Discussions</h3>
    @php
        $role = auth()->user()->role ?? null;
        $dash = $role==='admin' ? route('admin.dashboard') : ($role==='secretaire' ? route('secretaire.dashboard') : ($role==='medecin' ? route('medecin.dashboard') : ($role==='infirmier' ? route('infirmier.dashboard') : ($role==='patient' ? route('patient.dashboard') : route('login')))));
    @endphp
    <a href="{{ $dash }}" class="btn btn-outline-secondary btn-sm">Retour</a>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header">Partenaires</div>
        <div class="list-group list-group-flush">
          @php $uid = auth()->id(); @endphp
          @forelse($partners as $p)
          @php
            $unread = \App\Models\Message::whereNull('read_at')
              ->where('sender_id','!=',$uid)
              ->whereHas('conversation', function($q) use ($uid, $p){
                $q->where(function($qq) use ($uid,$p){ $qq->where('user_one_id',$uid)->where('user_two_id',$p->id); })
                  ->orWhere(function($qq) use ($uid,$p){ $qq->where('user_one_id',$p->id)->where('user_two_id',$uid); });
              })->count();
          @endphp
          <a href="{{ route('chat.index', ['partner_id'=>$p->id]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ ($partner && $partner->id===$p->id) ? 'active' : '' }}">
            <span>
              {{ $p->name }} <span class="text-muted small">({{ $p->role }})</span>
            </span>
            @if($unread>0)
              <span class="badge bg-danger">{{ $unread }}</span>
            @endif
          </a>
          @empty
          <div class="p-3 text-muted">Aucun partenaire disponible.</div>
          @endforelse
        </div>
      </div>
    </div>
    <div class="col-md-8">
      <div class="chat-box">
        <div class="p-2 border-bottom bg-white d-flex align-items-center justify-content-between">
          <div>
            <strong>Avec:</strong> {{ $partner?->name ?? '—' }}
          </div>
          <div id="typingIndicator" class="text-muted small" style="min-height:1rem;"></div>
        </div>
        <div class="chat-messages" id="chatMessages">
          @forelse($messages as $m)
          <div class="msg {{ $m->sender_id === $user->id ? 'me' : 'other' }}">
            <div class="bubble">
              <div class="small text-muted">{{ $m->sender->name }} • {{ $m->created_at->format('d/m/Y H:i') }} @if($m->sender_id === $user->id && $m->read_at) • <span class="text-success">lu</span>@endif</div>
              @if(!empty($m->file_path))
                @if($m->file_type==='image')
                  <div class="mb-1"><img src="{{ $m->file_path }}" alt="image" style="max-width:220px;border-radius:.5rem;"/></div>
                @elseif($m->file_type==='audio')
                  <div class="mb-1"><audio controls src="{{ $m->file_path }}"></audio></div>
                @else
                  <div class="mb-1"><a href="{{ $m->file_path }}" target="_blank">Télécharger le fichier</a></div>
                @endif
              @endif
              @if(!empty($m->body))
                <div>{{ $m->body }}</div>
              @endif
            </div>
          </div>
          @empty
          <div class="text-center text-muted">Aucun message.</div>
          @endforelse
          @if(method_exists($messages,'links'))
            <div class="mt-2 px-2">{{ $messages->appends(['partner_id'=>$partner?->id])->links() }}</div>
          @endif
        </div>
        <div class="chat-input">
          <form method="POST" action="{{ route('chat.send') }}" class="row g-2" enctype="multipart/form-data" id="sendForm">
            @csrf
            <input type="hidden" name="partner_id" value="{{ $partner?->id }}">
            <div class="col-6 col-md-6">
              <input type="text" name="body" class="form-control" placeholder="Votre message...">
            </div>
            <div class="col-3 col-md-3">
              <input type="file" name="file" id="fileInput" class="form-control" accept=".pdf,image/*,audio/*">
            </div>
            <div class="col-1 col-md-1 d-grid">
              <button type="button" id="btnRecord" class="btn btn-outline-danger" title="Message vocal"><i class="bi bi-mic-fill"></i></button>
            </div>
            <div class="col-2 col-md-2">
              <button class="btn btn-success w-100">Envoyer</button>
            </div>
            <div class="col-12 small text-muted" id="recStatus" style="display:none;">Enregistrement… <span class="rec-indicator">●</span></div>
          </form>
        </div>
      </div>
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
    let mediaRecorder = null; let recChunks = []; let stream = null;
    const typing = document.getElementById('typingIndicator');
    const input = document.querySelector('#sendForm input[name="body"]');
    let typingTimeout = null;
    function scrollBottom(){ if (box) { box.scrollTop = box.scrollHeight; } }
    scrollBottom();
    async function refreshMessages(){
      if(!partnerId) return;
      try{
        const r = await fetch(`{{ route('chat.messages') }}?partner_id=${partnerId}`, { headers: { 'Accept':'application/json' } });
        if(!r.ok) return;
        const data = await r.json();
        if(!Array.isArray(data.messages)) return;
        let html='';
        for(const m of data.messages){
          html += `<div class=\"msg ${m.mine?'me':'other'}\"><div class=\"bubble\">`+
                  `<div class=\"small text-muted\">${m.sender} • ${m.created_at}${m.mine && m.read ? ' • <span class=\\"text-success\\">lu</span>' : ''}</div>`+
                  (m.file_path ? (m.file_type==='image' ? `<div class=\"mb-1\"><img src=\"${m.file_path}\" style=\"max-width:220px;border-radius:.5rem;\"/></div>` : (m.file_type==='audio' ? `<div class=\"mb-1\"><audio controls src=\"${m.file_path}\"></audio></div>` : `<div class=\"mb-1\"><a href=\"${m.file_path}\" target=\"_blank\">Télécharger le fichier</a></div>`)) : '')+
                  (m.body ? `<div>${m.body}</div>` : '')+
                  `</div></div>`;
        }
        box.innerHTML = html;
        scrollBottom();
      }catch(e){ /* noop */ }
    }
    async function refreshTyping(){
      if(!partnerId || !typing) return;
      try{
        const r = await fetch(`{{ route('chat.typingStatus') }}?partner_id=${partnerId}`, { headers: { 'Accept':'application/json' } });
        if(!r.ok) return;
        const data = await r.json();
        typing.textContent = data.typing ? 'En train d\'écrire…' : '';
      }catch(e){ /* noop */ }
    }
    async function sendTyping(){
      if(!partnerId) return;
      try{
        await fetch(`{{ route('chat.typing') }}`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: new URLSearchParams({ partner_id: partnerId })
        });
      }catch(e){ /* noop */ }
    }
    if(input){
      input.addEventListener('input', () => {
        if(typingTimeout) clearTimeout(typingTimeout);
        sendTyping();
        typingTimeout = setTimeout(()=>{}, 2000);
      });
    }
    setInterval(refreshMessages, 10000);
    setInterval(refreshTyping, 2500);

    async function startRecording(){
      try{
        if(!partnerId){ alert('Sélectionnez un partenaire pour envoyer un message vocal.'); return; }
        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        recChunks = [];
        mediaRecorder = new MediaRecorder(stream);
        mediaRecorder.ondataavailable = e => { if(e.data.size>0) recChunks.push(e.data); };
        mediaRecorder.onstop = () => {
          const blob = new Blob(recChunks, { type: 'audio/webm' });
          const file = new File([blob], `voice_${Date.now()}.webm`, { type: 'audio/webm' });
          const dt = new DataTransfer(); dt.items.add(file); fileInput.files = dt.files;
          recStatus.style.display = 'none';
          recBtn.classList.remove('btn-danger');
          recBtn.classList.add('btn-outline-danger');
          recBtn.innerHTML = '<i class="bi bi-mic-fill"></i>';
          // Stop stream tracks
          try{ stream.getTracks().forEach(t=>t.stop()); }catch(_){ }
          // Envoi automatique comme WhatsApp
          const form = document.getElementById('sendForm');
          try{ form?.requestSubmit(); }catch(_){ form?.submit(); }
        };
        mediaRecorder.start();
        recStatus.style.display = '';
        recBtn.classList.remove('btn-outline-danger');
        recBtn.classList.add('btn-danger');
        recBtn.innerHTML = '<i class="bi bi-stop-fill"></i>';
      }catch(e){ console.warn('Audio record error', e); }
    }
    function stopRecording(){ if(mediaRecorder && mediaRecorder.state!=='inactive'){ mediaRecorder.stop(); } }
    recBtn?.addEventListener('click', ()=>{
      if(!mediaRecorder || mediaRecorder.state==='inactive'){ startRecording(); }
      else { stopRecording(); }
    });
  })();
</script>
@endsection
