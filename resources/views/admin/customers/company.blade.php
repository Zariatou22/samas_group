@extends('layouts.admin')

@section('title', 'Client '.$company->name)

@section('content')
    <p>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Client de {{ $customer->customer_name }}</h5>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-outline-primary">
                <i class="fa fa-edit"></i> Modifier
            </a>
        </div>
        <div class="card-block">
            <div class="table-responsive">
                @php
                    $docsBase = route('admin.customers.documents.index', $customer).'?company='.$company->id;
                @endphp
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nom du Client</th>
                            <th>Adresse du Client</th>
                            <th>Contact du Client</th>
                            <th>Nom responsable</th>
                            <th>N° RCCM</th>
                            <th>N° NIF/IFU</th>
                            <th>N° CNI/PASSEPORT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $company->name }}</td>
                            <td>{!! nl2br(e($company->address)) !!}</td>
                            <td>{{ $company->contact ?? '-' }}</td>
                            <td>{{ $company->owner_name ?? '-' }}</td>
                            <td><a href="{{ $docsBase }}&type=rccm">{{ $company->rccm ?? '-' }}</a></td>
                            <td><a href="{{ $docsBase }}&type=nif">{{ $company->nif ?? '-' }}</a></td>
                            <td><a href="{{ $docsBase }}&type=cni">{{ $company->cni ?? '-' }}</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
