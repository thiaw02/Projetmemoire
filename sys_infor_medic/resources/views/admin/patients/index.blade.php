@extends('layouts.app')

@section('content')
<h2>Liste des patients</h2>

<a href="{{ route('admin.patients.create') }}" class="btn btn-success mb-3">Ajouter un patient</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($patients as $patient)
        <tr>
            <td>{{ $patient->name }}</td>
            <td>{{ $patient->email }}</td>
            <td>
                <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-primary btn-sm">Modifier</a>
                <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirmer la suppression ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Supprimer</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
