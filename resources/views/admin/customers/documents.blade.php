@extends('layouts.admin')

@section('title', 'Documents '.$customer->customer_name)

@php
    $backUrl = $companyId
        ? route('admin.customers.companies.show', [$customer, $companyId])
        : route('admin.customers.index');
    $allDocsUrl = route('admin.customers.documents.index', $customer);
@endphp

@section('content')
    <p>
        <a href="{{ $backUrl }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.customers.edit', $customer) }}#documents" class="btn btn-outline-primary">
            <i class="fa fa-plus"></i> Ajouter un document
        </a>
    </p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($type || $companyId)
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span>
                Documents filtrés
                @if ($companyObj)
                    pour le client <strong>{{ $companyObj->name }}</strong>
                @endif
                @if ($type)
                    &mdash; type <strong>{{ $types[$type] ?? $type }}</strong>
                @endif
            </span>
            <a href="{{ $allDocsUrl }}">Voir tous les documents</a>
        </div>
    @endif

    <div class="card">
        <div class="card-header"><h5>Documents de {{ $customer->customer_name }}</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>LIBELLÉ</th>
                            <th>CLIENT</th>
                            <th>TYPE</th>
                            <th>FICHIER</th>
                            <th>AJOUTÉ LE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($documents as $document)
                            <tr>
                                <td>{{ $document->name }}</td>
                                <td>{{ $document->clientCompany->name ?? '-' }}</td>
                                <td>{{ $types[$document->type] ?? '-' }}</td>
                                <td>
                                    @if ($document->filename)
                                        <a href="{{ asset('storage/customers/'.$document->filename) }}" target="_blank">Voir <i class="fa fa-external-link"></i></a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ optional($document->created)->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.customers.edit', $customer) }}#documents" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.customers.documents.destroy', [$customer, $document]) }}" class="d-inline" onsubmit="return confirm('Supprimer ce document ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Supprimer"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Aucun document.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
