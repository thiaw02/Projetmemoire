@extends('layouts.app')

@section('content')
<div class="container-fluid">
     {{-- Bouton retour vers le dashboard --}}
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('secretaire.dashboard') }}" class="btn btn-danger">
            <i class="bi bi-arrow-left"></i> <!-- icône optionnelle -->
            Retour vers le dashboard
        </a>
    </div>

<div class="card shadow p-4">
    <h3 class="text-success mb-3">📁 Dossiers Administratifs</h3>
    <p class="text-muted">Ci-dessous la liste fictive des dossiers. Cette section sera reliée à la base de données ultérieurement.</p>
    
    <table class="table table-bordered table-hover mt-3">
        <thead class="table-success">
            <tr>
                <th>#</th>
                <th>Nom du Patient</th>
                <th>Date de Création</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>Awa Ndiaye</td>
                <td>15/07/2025</td>
                <td>En cours</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Moussa Fall</td>
                <td>14/07/2025</td>
                <td>Complet</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
