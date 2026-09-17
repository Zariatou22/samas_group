@extends('layouts.admin')

@section('title', $title)

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary">&larr;&nbsp;Retour à l'accueil</a>
    </p>
    <h3>{{ $title }}</h3>
    <div class="row">
        @forelse ($cards as $card)
            <div class="col-xl-3 col-lg-3 col-md-4">
                <div class="card mb-3 no-select click-to-run" role="button" data-href="{{ $card['url'] }}">
                    <div class="card-header bg-{{ $card['color'] ?? $defaultColor ?? 'primary' }}">
                        <div class="card-header-icon">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                    </div>
                    <div class="card-body text-center bg-{{ $card['color'] ?? $defaultColor ?? 'primary' }} text-white p-0">
                        <div class="bg-dark" style="height: 50px; width: 100%; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px">
                            <h5 class="text-truncate font-weight-semibold mt-3 text-center" style="line-height: 50px">{{ $card['label'] }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">Aucun élément pour le moment.</p>
            </div>
        @endforelse
    </div>
@endsection
