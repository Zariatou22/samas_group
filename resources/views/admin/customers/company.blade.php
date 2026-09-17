@extends('layouts.admin')

@section('title', 'Client '.$company->name)

@php
    $infos = [
        ['icon' => 'fa-building', 'label' => 'Nom du Client', 'value' => $company->name],
        ['icon' => 'fa-map-marker', 'label' => 'Adresse du Client', 'value' => $company->address, 'nl2br' => true],
        ['icon' => 'fa-phone', 'label' => 'Contact du Client', 'value' => $company->contact],
        ['icon' => 'fa-user', 'label' => 'Nom responsable', 'value' => $company->owner_name],
        ['icon' => 'fa-file-text', 'label' => 'N° RCCM', 'value' => $company->rccm, 'docType' => 'rccm'],
        ['icon' => 'fa-file-text', 'label' => 'N° NIF/IFU', 'value' => $company->nif, 'docType' => 'nif'],
        ['icon' => 'fa-id-card', 'label' => 'N° CNI/PASSEPORT', 'value' => $company->cni, 'docType' => 'cni'],
    ];
    // Documents déjà uploadés, regroupés par type pour affichage inline sous RCCM/NIF/CNI
    $filesByType = $files->groupBy('type');
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
                    @php
                        $docType = $info['docType'] ?? null;
                        $typeFiles = $docType ? ($filesByType->get($docType) ?? collect()) : collect();
                    @endphp
                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center py-3">
                        <span class="text-muted">
                            <i class="fa {{ $info['icon'] }} fa-fw mr-2 text-primary"></i>{{ $info['label'] }}
                        </span>
                        <span class="font-weight-bold text-right">
                            @if (empty($info['value']))
                                &mdash;
                            @elseif ($docType)
                                <a href="#docs-{{ $docType }}" data-toggle="collapse" title="Afficher/masquer les documents">
                                    {{ $info['value'] }}&nbsp;<i class="fa fa-folder-open"></i>
                                    @if ($typeFiles->isNotEmpty())
                                        <span class="badge badge-primary">{{ $typeFiles->count() }}</span>
                                    @endif
                                </a>
                            @elseif (! empty($info['nl2br']))
                                {!! nl2br(e($info['value'])) !!}
                            @else
                                {{ $info['value'] }}
                            @endif
                        </span>
                    </li>
                    @if ($docType)
                        <li class="list-group-item collapse p-0" id="docs-{{ $docType }}">
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    @forelse ($typeFiles as $file)
                                        <tr>
                                            <td class="align-middle">{{ $file->name }}</td>
                                            <td class="align-middle text-center" style="width:11rem;">{{ optional($file->created)->format('d/m/Y') }}</td>
                                            <td class="align-middle text-center" style="width:15rem;">
                                                @if ($file->filename)
                                                    <a href="{{ asset('storage/customers/'.$file->filename) }}" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-download"></i> Télécharger</a>
                                                @endif
                                                <form method="POST" action="{{ route('admin.customers.documents.destroy', [$customer, $file]) }}" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-muted text-center py-3">Aucun document pour ce type</td>
                                        </tr>
                                    @endforelse
                                    <tr>
                                        <td colspan="3" class="text-right">
                                            <a href="{{ route('admin.customers.edit', $customer) }}#documents" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus"></i> Ajouter un document</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@endsection
