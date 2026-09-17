@extends('layouts.admin')

@section('title', 'Clients de '.$customer->customer_name)

@section('content')
    <p>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.customers.edit', $customer) }}#clients" class="btn btn-outline-primary">
            <i class="fa fa-plus"></i> Nouveau
        </a>
    </p>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header"><h5>Clients de {{ $customer->customer_name }}</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th style="width: 3em;" class="text-center">#</th>
                            <th>Nom du client</th>
                            <th>Contact du client</th>
                            <th>N° RCCM</th>
                            <th>N° NIF/IFU</th>
                            <th>N° CNI/Passeport</th>
                            <th>Responsable</th>
                            <th style="width: 12em;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($companies as $key => $company)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td><a href="{{ route('admin.customers.companies.show', [$customer, $company]) }}">{{ $company->name }}</a></td>
                                <td>{{ $company->contact ?? '-' }}</td>
                                <td>{{ $company->rccm ?? '-' }}</td>
                                <td>{{ $company->nif ?? '-' }}</td>
                                <td>{{ $company->cni ?? '-' }}</td>
                                <td>{{ $company->owner_name ?? '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.customers.edit', $customer) }}#clients" class="btn btn-sm btn-outline-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                                    <a href="{{ route('admin.customers.transfer.form', $customer) }}" class="btn btn-sm btn-warning" title="Transférer"><i class="fa fa-exchange"></i></a>
                                    <a href="{{ route('admin.customers.documents.index', $customer) }}?company={{ $company->id }}" class="btn btn-sm btn-secondary" title="Documents"><i class="fa fa-folder-open"></i></a>
                                    <form method="POST" action="{{ route('admin.customers.companies.destroy', [$customer, $company]) }}" class="d-inline" onsubmit="return confirm('Archiver ce client ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center">Aucun client trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
