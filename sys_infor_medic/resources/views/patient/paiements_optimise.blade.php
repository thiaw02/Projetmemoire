@extends('layouts.app')

@section('content')
<style>
  :root {
    --primary: #10b981;
    --gray-100: #f9fafb;
    --gray-200: #e5e7eb;
    --gray-700: #374151;
    --radius-xl: 16px;
    --shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    --transition: 0.25s ease-in-out;
  }

  body > .container {
    max-width: 1400px !important;
  }

  .card {
    border: none;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow);
    overflow: hidden;
    background: #fff;
  }

  .card-header {
    background: var(--primary);
    color: #fff;
    font-weight: 600;
    font-size: 1.25rem;
  }

  .card-body {
    padding: 1rem 1.5rem;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  th {
    background: var(--gray-100);
    color: var(--gray-700);
    padding: 0.75rem;
    text-transform: uppercase;
    font-size: 0.85rem;
    border-bottom: 2px solid var(--gray-200);
  }

  td {
    padding: 0.75rem;
    vertical-align: middle;
  }

  tr:hover {
    background: #f0fdf4;
    transition: var(--transition);
  }

  .btn-primary {
    background: var(--primary);
    border: none;
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
    border-radius: 8px;
    transition: var(--transition);
  }

  .btn-primary:hover {
    background: #0ea572;
  }

  .btn-secondary {
    background: #6b7280;
    border: none;
    border-radius: 8px;
    color: white;
    padding: 0.4rem 0.8rem;
  }

  .btn-secondary:hover {
    background: #4b5563;
  }

  @media (max-width: 768px) {
    table thead {
      display: none;
    }

    table, table tbody, table tr, table td {
      display: block;
      width: 100%;
    }

    table tr {
      margin-bottom: 1rem;
      background: #fff;
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow);
      padding: 1rem;
    }

    table td {
      border: none;
      display: flex;
      justify-content: space-between;
      font-size: 0.9rem;
    }

    table td::before {
      content: attr(data-label);
      font-weight: 600;
      color: var(--gray-700);
    }
  }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h3>🩺 Dossiers Médicaux des Patients</h3>
  <a href="{{ route('medecin.dashboard') }}" class="btn btn-secondary">← Retour au dashboard</a>
</div>

@if($patients->isEmpty())
  <p id="no-patients" class="text-center text-muted">Aucun patient pour le moment.</p>
@endif

<div class="card">
  <div class="card-header">
    Liste des patients
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Sexe</th>
            <th>Date de naissance</th>
            <th>Téléphone</th>
            <th>Groupe sanguin</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="patients-rows">
          @foreach($patients as $patient)
          <tr data-id="{{ $patient->id }}">
            <td data-label="Nom">{{ $patient->nom }}</td>
            <td data-label="Prénom">{{ $patient->prenom }}</td>
            <td data-label="Sexe">{{ $patient->sexe }}</td>
            <td data-label="Date de naissance">{{ \Carbon\Carbon::parse($patient->date_naissance)->format('d/m/Y') }}</td>
            <td data-label="Téléphone">{{ $patient->telephone ?? '-' }}</td>
            <td data-label="Groupe sanguin">{{ $patient->groupe_sanguin ?? '-' }}</td>
            <td data-label="Actions">
              <a href="{{ route('medecin.patients.show', ['patientId' => $patient->id]) }}" class="btn btn-sm btn-primary">Ouvrir</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const rowsContainer = document.getElementById('patients-rows');
  const noPatientsEl = document.getElementById('no-patients');
  const fetchUrl = "{{ route('patients.live') }}";

  function escapeHtml(str) {
    return String(str === null || str === undefined ? '' : str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  async function refreshPatients() {
    try {
      const res = await fetch(fetchUrl, { method: 'GET', headers: { 'Accept': 'application/json' }});
      if (!res.ok) return console.error('Erreur HTTP:', res.status);
      const data = await res.json();

      if (Array.isArray(data) && data.length > 0) {
        if (noPatientsEl) noPatientsEl.style.display = 'none';

        let html = '';
        data.forEach(p => {
          const date = p.date_naissance ? new Date(p.date_naissance).toLocaleDateString() : '-';
          html += `
            <tr data-id="${p.id}">
              <td data-label="Nom">${escapeHtml(p.nom || '-')}</td>
              <td data-label="Prénom">${escapeHtml(p.prenom || '-')}</td>
              <td data-label="Sexe">${escapeHtml(p.sexe || '-')}</td>
              <td data-label="Date de naissance">${escapeHtml(date)}</td>
              <td data-label="Téléphone">${escapeHtml(p.telephone || '-')}</td>
              <td data-label="Groupe sanguin">${escapeHtml(p.groupe_sanguin || '-')}</td>
              <td data-label="Actions">
                <a href="/medecin/patients/${p.id}" class="btn btn-sm btn-primary">Ouvrir</a>
              </td>
            </tr>`;
        });
        rowsContainer.innerHTML = html;
      } else {
        rowsContainer.innerHTML = '';
        if (noPatientsEl) noPatientsEl.style.display = 'block';
      }
    } catch (err) {
      console.error('Erreur AJAX:', err);
    }
  }

  refreshPatients();
  setInterval(refreshPatients, 10000); // rafraîchissement toutes les 10 secondes
});
</script>
@endsection
