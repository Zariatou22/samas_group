@extends('layouts.admin')

@section('title', 'Modifier '.$group->name)

@section('content')
    <p>
        <a href="{{ route('admin.groups.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.groups.update', $group) }}">
        @csrf
        @method('PUT')
        @include('admin.groups._form')
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
