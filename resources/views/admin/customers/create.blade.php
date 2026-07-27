@extends('layouts.admin')

@section('title', 'Nouveau mandataire')

@section('content')
    <p>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.customers.store') }}">
        @csrf
        @include('admin.customers._form')
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
