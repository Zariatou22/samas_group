@extends('layouts.admin')

@section('title', 'Client '.$company->name)

@php
    $docsBase = route('admin.customers.documents.index', $customer).'?company='.$company->id;
    $infos = [
        ['icon' => 'fa-building', 'label' => 'Nom du Client', 'value' => $company->name],
        ['icon' => 'fa-map-marker', 'label' => 'Adresse du Client', 'value' => $company->address, 'nl2br' => true],
        ['icon' => 'fa-phone', 'label' => 'Contact du Client', 'value' => $company->contact],
        ['icon' => 'fa-user', 'label' => 'Nom responsable', 'value' => $company->owner_name],
        ['icon' => 'fa-file-text', 'label' => 'N° RCCM', 'value' => $company->rccm, 'docType' => 'rccm'],
        ['icon' => 'fa-file-text', 'label' => 'N° NIF/IFU', 'value' => $company->nif, 'docType' => 'nif'],
        ['icon' => 'fa-id-card', 'label' => 'N° CNI/PASSEPORT', 'value' => $company->cni, 'docType' => 'cni'],
    ];
@endphp

@section('content')
    <p>
        <a href="{{ route('admin.customers.companies.index', $customer) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Client de {{ $customer->customer_name }}</h5>
            <a href="{{ route('admin.customers.edit', $customer) }}#clients" class="btn btn-sm btn-outline-primary">
                <i class="fa fa-edit"></i> Modifier
            </a>
        </div>
        <div class="card-block p-0">
            <ul class="list-group list-group-flush">
                @foreach ($infos as $info)
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3">
                        <span class="text-muted">
                            <i class="fa {{ $info['icon'] }} fa-fw mr-2 text-primary"></i>{{ $info['label'] }}
                        </span>
                        <span class="font-weight-bold text-right">
                            @if (empty($info['value']))
                                &mdash;
                            @elseif (isset($info['docType']))
                                <a href="{{ $docsBase }}&type={{ $info['docType'] }}" title="Voir les documents">
                                    {{ $info['value'] }}&nbsp;<i class="fa fa-folder-open"></i>
                                </a>
                            @elseif (! empty($info['nl2br']))
                                {!! nl2br(e($info['value'])) !!}
                            @else
                                {{ $info['value'] }}
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
