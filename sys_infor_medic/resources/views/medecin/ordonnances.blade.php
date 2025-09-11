@extends('layouts.app')

@section('content')
<h3>💊 Mes ordonnances</h3>
<table class="table table-hover">
    <thead>
        <tr>
            <th>Patient</th>
            <th>Médicaments</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ordonnances as $o)
            <tr>
                <td>{{ $o->patient->nom }} {{ $o->patient->prenom }}</td>
                <td>{{ $o->contenu }}</td>
                <td>{{ $o->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{ $ordonnances->links() }}
@endsection
