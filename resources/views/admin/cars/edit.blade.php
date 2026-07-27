@extends('layouts.admin')

@section('title', 'Modifier '.$car->full_registration)

@section('content')
    <p>
        <a href="{{ route('admin.cars.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.cars.update', $car) }}">
        @csrf
        @method('PUT')
        @include('admin.cars._form')
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection
