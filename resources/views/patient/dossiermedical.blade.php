@extends('layouts.app')

@section('content')
<div class="container bg-white p-4 rounded shadow-sm">
    <h2 class="text-success mb-4">📁 Mon dossier médical</h2>

    {{-- 🔹 Consultations --}}
    <h3 class="mt-4">🩺 Consultations</h3>
    @forelse($consultations as $c)
        <div class="card bg-light mb-3">
            <div class="card-header text-success">💉 Consultation du {{ $c->date_consultation }}</div>
            <div class="card-body">
                <p><strong>Diagnostic :</strong> {{ $c->diagnostic }}</p>
                <p><strong>Traitement :</strong> {{ $c->traitement }}</p>

                {{-- 🔹 Ordonnances --}}
                <p>💊 <strong>Ordonnances :</strong></p>
                <ul>
                    @forelse($c->ordonnances as $o)
                        <li>{{ $o->medicament }} - {{ $o->dose }}</li>
                    @empty
                        <li class="text-muted">Aucune ordonnance enregistrée</li>
                    @endforelse
                </ul>

                {{-- 🔹 Analyses --}}
                <p>🧪 <strong>Analyses :</strong></p>
                <ul>
                    @forelse($c->analyses as $a)
                        <li>{{ $a->type }} : {{ $a->resultat }}</li>
                    @empty
                        <li class="text-muted">Aucune analyse enregistrée</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @empty
        <p class="text-muted">Aucune consultation enregistrée</p>
    @endforelse
</div>
@endsection
