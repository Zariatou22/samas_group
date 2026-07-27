@extends('layouts.admin')

@section('title', 'Modifier '.trim($targetUser->prenoms.' '.$targetUser->nom))

@section('content')
    <p>
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.users.update', $targetUser) }}">
        @csrf
        @method('PUT')
        @include('admin.users._form')
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
