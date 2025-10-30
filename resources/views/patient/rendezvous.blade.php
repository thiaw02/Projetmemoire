@extends('layouts.app')

@section('content')
<style>
  body > .container { max-width: 1500px !important; }
  .sidebar-sticky { position: sticky; top: 1rem; }
</style>
<div class="bg-white p-4 rounded shadow-sm">

    <h2 class="text-success mb-4">👋 Bienvenue sur votre espace Patient</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <h4 class="mb-3">📅 Prendre un rendez-vous</h4>

    {{-- Calendrier --}}
    <div id="calendar" class="border rounded p-3 mb-4 bg-light"></div>

    {{-- Formulaire --}}
    <form action="{{ route('patient.storeRendez') }}" method="POST">
        @csrf
        <input type="hidden" name="date" id="dateSelected">

        <div class="mb-3">
            <label for="service_id" class="form-label">Service</label>
            <select id="service_id" name="service_id" class="form-select" required>
                <option value="">Sélectionnez un service</option>
                @foreach(($services ?? []) as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
            <div class="form-text">Choisissez le service pour voir les médecins disponibles.</div>
        </div>

        <div class="mb-3">
            <label for="medecin_id" class="form-label">Médecin</label>
            <select name="medecin_id" id="medecin_id" class="form-select" required disabled>
                <option value="">Choisissez d'abord un service</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="heure" class="form-label">Heure</label>
            <input type="time" name="heure" id="heure" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="motif" class="form-label">Motif</label>
            <textarea name="motif" id="motif" class="form-control" rows="3" placeholder="Motif du rendez-vous..." required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Envoyer la demande</button>
    </form>

    {{-- Liste des rendez-vous --}}
    <hr>
    <h4 class="mt-4">📜 Mes rendez-vous <small class="text-muted">{{ method_exists($rendezVous,'total') ? '(' . $rendezVous->total() . ')' : '' }}</small></h4>
    <ul>
        @forelse($rendezVous as $rdv)
            <li>{{ $rdv->date }} → {{ $rdv->motif }} 
                <span class="badge {{ in_array(strtolower((string)$rdv->statut), ['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array(strtolower((string)$rdv->statut), ['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ str_replace('_',' ', $rdv->statut) }}</span>
            </li>
        @empty
            <li>Aucun rendez-vous enregistré.</li>
        @endforelse
    </ul>
    @if(method_exists($rendezVous,'links'))
      <div class="d-flex justify-content-center mt-2">
        {{ $rendezVous->appends(request()->query())->links('pagination.custom') }}
      </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');

    var events = [
        @foreach($rendezVous as $rdv)
            {
                title: "{{ $rdv->motif }}",
                start: "{{ $rdv->date }}",
                allDay: true,
                color: "#28a745"
            },
        @endforeach
    ];

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        selectable: true,
        events: events,
        dateClick: function(info) {
            // Vérifie si la date est déjà prise
            var dateTaken = events.some(e => e.start === info.dateStr);
            if(dateTaken){
                alert('Cette date est déjà réservée !');
                return;
            }
            // Remplit le champ caché
            document.getElementById('dateSelected').value = info.dateStr;
            alert('Vous avez sélectionné le ' + info.dateStr);
        }
    });

    calendar.render();

    // Chargement dynamique des médecins par service
    const serviceSelect = document.getElementById('service_id');
    const medecinSelect = document.getElementById('medecin_id');
    serviceSelect.addEventListener('change', async function() {
        const serviceId = this.value;
        medecinSelect.innerHTML = '<option value="">Chargement...</option>';
        medecinSelect.disabled = true;
        if (!serviceId) {
            medecinSelect.innerHTML = '<option value="">Choisissez d\'abord un service</option>';
            return;
        }
        try {
            const res = await fetch(`/patient/services/${serviceId}/medecins`);
            const data = await res.json();
            medecinSelect.innerHTML = '<option value="">Sélectionnez un médecin</option>';
            (data.data || []).forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id; opt.textContent = m.name;
                medecinSelect.appendChild(opt);
            });
            medecinSelect.disabled = false;
        } catch (e) {
            medecinSelect.innerHTML = '<option value="">Erreur de chargement</option>';
        }
    });
});
</script>
@endsection
