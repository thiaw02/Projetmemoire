@extends('layouts.app')

@section('content')
<h2>Liste des utilisateurs</h2>

<a href="{{ route('admin.users.create') }}" class="btn btn-success mb-3">Ajouter un utilisateur</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Spécialité</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>{{ $user->specialite ?? '-' }}</td>
            <td>
                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">Modifier</a>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Confirmer la suppression ?');">
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
