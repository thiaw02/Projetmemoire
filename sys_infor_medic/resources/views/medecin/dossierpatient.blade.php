@extends('layouts.app')

@section('content')
<h3>📁 Liste des patients suivis</h3>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Diagnostic</th>
            <th>Date mise à jour</th>
        </tr>
    </thead>
    <tbody>
        @foreach($patients as $dossier)
            <tr>
                <td>{{ $dossier->patient->nom }}</td>
                <td>{{ $dossier->patient->prenom }}</td>
                <td>{{ $dossier->diagnostic }}</td>
                <td>{{ $dossier->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $patients->links() }}
@endsection
