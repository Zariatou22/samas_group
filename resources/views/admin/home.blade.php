@extends('layouts.admin')

@section('title', 'Accueil')

@push('styles')
    <style>
        /* Le contenu doit défiler indépendamment, même si la sidebar reste fixe */
        html, body {
            height: auto;
            overflow-y: auto;
        }
        .pcoded-content {
            overflow-y: visible;
        }

        .home-section-title {
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: 0.02em;
        }
        .home-card {
            display: block;
            width: 100%;
        }
        .home-card .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out;
        }
        .home-card:hover .card {
            transform: translateY(-5px);
            box-shadow: 0 10px 18px rgba(0, 0, 0, 0.2);
        }
        .home-card-icon {
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .home-card-icon i {
            transition: transform 0.2s ease-out;
        }
        .home-card:hover .home-card-icon i {
            transform: scale(1.15);
        }
        .home-card-label-wrap {
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
        }
        .home-card-label-wrap h5 {
            line-height: 1.1;
            margin: 0;
            font-size: 0.8rem;
        }

        .home-card-grid > .col-6 {
            margin-bottom: 1.25rem;
            padding-left: 8px;
            padding-right: 8px;
        }
        .home-section-col {
            margin-bottom: 2.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        @foreach ($sections as $section)
            @if ($section['visible'])
                <div class="col-lg-3 col-md-6 col-sm-6 home-section-col">
                    <h5 class="home-section-title">{{ $section['title'] }}</h5>
                    <div class="row home-card-grid">
                        @foreach ($section['cards'] as $card)
                            <div class="col-6">
                                <a href="{{ $card['url'] }}" class="text-decoration-none home-card">
                                    <div class="card mb-0">
                                        <div class="card-header bg-{{ $section['color'] }} home-card-icon">
                                            <i class="fa {{ $card['icon'] }} fa-lg text-white"></i>
                                        </div>
                                        <div class="card-body text-center bg-{{ $section['color'] }} text-white p-0">
                                            <div class="bg-dark home-card-label-wrap" style="border-bottom-left-radius: 5px; border-bottom-right-radius: 5px">
                                                <h5 class="text-truncate font-weight-semibold text-white">
                                                    {{ $card['label'] }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
@endsection
