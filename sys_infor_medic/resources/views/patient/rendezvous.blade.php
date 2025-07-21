@extends('layouts.app')

@section('content')
    <h3>Prendre un rendez-vous</h3>
    <form>
        <div class="mb-3">
            <label for="date" class="form-label">Date souhaitée</label>
            <input type="date" class="form-control" id="date">
        </div>
        <button class="btn btn-primary">Soumettre</button>
    </form>
@endsection
