@extends('layouts.app')

@section('content')
<h3 class="mb-4">🩺 Mes consultations</h3>

<div id="calendar"></div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="consultationModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Gestion Consultation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="consultationForm">
          @csrf
          <input type="hidden" id="consultation_id">
          
          <div class="mb-3">
            <label>Patient</label>
            <select id="patient_id" class="form-control">
              @foreach($patients as $p)
                <option value="{{ $p->id }}">{{ $p->nom }} {{ $p->prenom }}</option>
              @endforeach
            </select>
          </div>
          
          <div class="mb-3">
            <label>Date & heure</label>
            <input type="datetime-local" id="date_consultation" class="form-control">
          </div>
          
          <div class="mb-3">
            <label>Symptômes</label>
            <input type="text" id="symptomes" class="form-control">
          </div>

          <div class="mb-3">
            <label>Diagnostic</label>
            <input type="text" id="diagnostic" class="form-control">
          </div>

          <div class="mb-3">
            <label>Traitement</label>
            <input type="text" id="traitement" class="form-control">
          </div>
          
          <div class="mb-3">
            <label>Statut</label>
            <select id="statut" class="form-control">
                <option value="En attente">En attente</option>
                <option value="En cours">En cours</option>
                <option value="Terminée">Terminée</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="saveBtn" class="btn btn-success">💾 Enregistrer</button>
        <button id="deleteBtn" class="btn btn-danger d-none">🗑 Supprimer</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
    #calendar {
        max-width: 1100px;
        margin: 0 auto;
        height: 600px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');
    let modal = new bootstrap.Modal(document.getElementById('consultationModal'));

    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: '{{ route("medecin.consultations.events") }}',
        selectable: true,
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        // Quand on sélectionne une date
        select: function(info) {
            document.getElementById('consultationForm').reset();
            document.getElementById('consultation_id').value = '';
            document.getElementById('date_consultation').value = info.startStr.substring(0, 16);
            document.getElementById('deleteBtn').classList.add('d-none');
            modal.show();
        },

        // Quand on clique sur un event
        eventClick: function(info) {
            let event = info.event;
            document.getElementById('consultation_id').value = event.id;
            document.getElementById('date_consultation').value = event.start.toISOString().slice(0,16);

            document.getElementById('patient_id').value = event.extendedProps.patient_id || '';
            document.getElementById('symptomes').value = event.extendedProps.symptomes || '';
            document.getElementById('diagnostic').value = event.extendedProps.diagnostic || '';
            document.getElementById('traitement').value = event.extendedProps.traitement || '';
            document.getElementById('statut').value = event.extendedProps.statut || 'En attente';

            document.getElementById('deleteBtn').classList.remove('d-none');
            modal.show();
        }
    });

    calendar.render();

    // Sauvegarde
    document.getElementById('saveBtn').addEventListener('click', function() {
        let id = document.getElementById('consultation_id').value;
        let data = {
            patient_id: document.getElementById('patient_id').value,
            date_consultation: document.getElementById('date_consultation').value,
            symptomes: document.getElementById('symptomes').value,
            diagnostic: document.getElementById('diagnostic').value,
            traitement: document.getElementById('traitement').value,
            statut: document.getElementById('statut').value,
        };

        let url = id 
            ? '/medecin/consultations/update/' + id 
            : '/medecin/consultations/store';

        let method = id ? 'PUT' : 'POST';

        fetch(url, {
            method: method,
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            calendar.refetchEvents(); 
            modal.hide();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la sauvegarde');
        });
    });

    // Suppression
    document.getElementById('deleteBtn').addEventListener('click', function() {
        if(!confirm('Êtes-vous sûr de vouloir supprimer cette consultation ?')) {
            return;
        }
        
        let id = document.getElementById('consultation_id').value;
        fetch('/medecin/consultations/delete/' + id, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
            calendar.refetchEvents(); 
            modal.hide();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la suppression');
        });
    });
});
</script>
@endpush