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
    <form action="{{ route('rendez.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" id="dateSelected">

        <div class="mb-3">
            <label for="motif" class="form-label">Motif</label>
            <textarea name="motif" id="motif" class="form-control" rows="3" placeholder="Motif du rendez-vous..." required></textarea>
        </div>

        <button type="submit" class="btn btn-success">Envoyer la demande</button>
    </form>

    {{-- Liste des rendez-vous --}}
    <hr>
    <h4 class="mt-4">📜 Mes rendez-vous</h4>
    <ul>
        @forelse($rendezVous as $rdv)
            <li>{{ $rdv->date }} → {{ $rdv->motif }} 
                <span class="badge {{ in_array(strtolower((string)$rdv->statut), ['confirmé','confirme','confirmée','confirmee']) ? 'bg-success' : (in_array(strtolower((string)$rdv->statut), ['annulé','annule','annulée','annulee']) ? 'bg-secondary' : 'bg-warning text-dark') }}">{{ str_replace('_',' ', $rdv->statut) }}</span>
            </li>
        @empty
            <li>Aucun rendez-vous enregistré.</li>
        @endforelse
    </ul>
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
});
</script>
@endsection
