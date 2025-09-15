@extends('layouts.app')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <h2 class="text-success mb-4">📁 Mon dossier médical</h2>

    @foreach($consultations as $c)
        <div class="card bg-light mb-3">
            <div class="card-header text-success">💉 Consultation du {{ $c->date_consultation }}</div>
            <div class="card-body">
                <p><strong>Diagnostic :</strong> {{ $c->diagnostic }}</p>
                <p><strong>Traitement :</strong> {{ $c->traitement }}</p>

                {{-- Ordonnances --}}
                <p>📝 <strong>Ordonnances :</strong></p>
                <ul>
                    @foreach($c->ordonnances as $o)
                        <li>{{ $o->medicament }} - {{ $o->dose }}</li>
                    @endforeach
                </ul>

                {{-- Analyses --}}
                <p>🔬 <strong>Analyses :</strong></p>
                <ul>
                    @foreach($c->analyses as $a)
                        <li>{{ $a->type }} : {{ $a->resultat }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endforeach
</div>
@endsection
