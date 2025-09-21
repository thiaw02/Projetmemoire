@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-danger">Déconnexion</button>
        </form>
    </div>
    <div class="container bg-white p-4 rounded shadow-sm">

        <h2 class="text-success mb-4">👋 Bienvenue sur votre espace Patient</h2>

        {{-- Onglets de navigation --}}
        <ul class="nav nav-tabs mb-3" id="patientTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-success" id="rdv-tab" data-bs-toggle="tab" data-bs-target="#rdv" type="button" role="tab">📅 Prendre un rendez-vous</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-success" id="dossier-tab" data-bs-toggle="tab" data-bs-target="#dossier" type="button" role="tab">📁 Mon dossier médical</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-success" id="historique-tab" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab">📜 Historique</button>
            </li>
        </ul>

        <div class="tab-content" id="patientTabContent">
            {{-- Onglet RDV --}}
            <div class="tab-pane fade show active" id="rdv" role="tabpanel">
                <h4 class="mb-3">Sélectionner une date</h4>
                <div id="calendar" class="border rounded p-3 bg-light"></div>
                <form class="mt-4">
                    <div class="mb-3">
                        <label for="motif" class="form-label">Motif</label>
                        <textarea name="motif" class="form-control" rows="3" placeholder="Motif du rendez-vous..."></textarea>
                    </div>
                    <button class="btn btn-success">Envoyer la demande</button>
                </form>
            </div>

            {{-- Onglet Dossier Médical --}}
            <div class="tab-pane fade" id="dossier" role="tabpanel">
                <h4 class="mb-3">📁 Mon dossier médical</h4>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-header text-success">📝 Ordonnances</div>
                            <div class="card-body">
                                <p>- Paracétamol 500mg <br> - Ibuprofène 400mg</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-header text-success">🔬 Analyses</div>
                            <div class="card-body">
                                <p>- Bilan sanguin : OK<br> - Test urinaire : OK</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light mb-3">
                            <div class="card-header text-success">💉 Consultations</div>
                            <div class="card-body">
                                <p>- 12/06/2025 : Fièvre<br> - 20/07/2025 : Maux de tête</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Onglet Historique --}}
            <div class="tab-pane fade" id="historique" role="tabpanel">
                <h4 class="mb-3">📜 Historique de mes consultations</h4>
                <table class="table table-striped table-bordered bg-light">
                    <thead class="table-success">
                        <tr>
                            <th>Date</th>
                            <th>Motif</th>
                            <th>Médecin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>2025-07-10</td>
                            <td>Douleurs abdominales</td>
                            <td>Dr. Ndiaye</td>
                        </tr>
                        <tr>
                            <td>2025-06-15</td>
                            <td>Fatigue chronique</td>
                            <td>Dr. Sow</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4 text-success">👤 Mes informations personnelles</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Nom :</strong> Thiaw</li>
                    <li class="list-group-item"><strong>Prénom :</strong> Lamine</li>
                    <li class="list-group-item"><strong>Adresse :</strong> Dakar, Sénégal</li>
                    <li class="list-group-item"><strong>Numéro de téléphone :</strong> +221 77 123 4567</li>
                    <li class="list-group-item"><strong>Date de naissance :</strong> 1999-03-15</li>
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        if (calendarEl) {
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                dateClick: function(info) {
                    alert('Vous avez sélectionné le ' + info.dateStr);
                }
            });
            calendar.render();
        }
    });
</script>
@endsection
