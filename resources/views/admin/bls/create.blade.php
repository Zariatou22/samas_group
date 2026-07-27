@extends('layouts.admin')

@section('title', 'Nouvel arrivage')

@section('content')
    <p>
        <a href="{{ route('admin.bls.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.bls.store') }}">
        @csrf
        @include('admin.bls._form')
    </form>
@endsection
