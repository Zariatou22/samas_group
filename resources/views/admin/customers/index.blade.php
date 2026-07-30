@extends('layouts.admin')

@section('title', 'Mandataires')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouveau mandataire</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des mandataires</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="customerTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">CONTACT</th>
                            <th class="text-center align-middle">EMAIL</th>
                            <th class="text-center align-middle">CLIENTS</th>
                            <th class="text-center align-middle">DOCUMENTS</th>
                            <th class="text-center align-middle">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            $('#customerTable').DataTable({
                ajax: '{{ route('admin.customers.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/customers/${d.id}/edit">${d.customer_name}</a>`},
                    {data: (d) => d.customer_contact ?? '-'},
                    {data: (d) => d.email ?? '-'},
                    {data: (d) => d.companies.length ? d.companies.map((c) => `<a href="/admin/customers/${d.id}/edit">${c.name}</a>`).join('<br>') : '-'},
                    {data: (d) => `<span class="badge badge-secondary">${d.documents_count}</span>`},
                    {data: (d) => `
                        <a href="/admin/customers/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <a href="/admin/customers/${d.id}/transfer" class="btn btn-sm btn-warning" title="Transférer le portefeuille"><i class="fa fa-exchange"></i></a>
                        <form method="POST" action="/admin/customers/${d.id}" class="d-inline" onsubmit="return confirm('Archiver ce mandataire ?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-sm btn-danger" title="Archiver"><i class="fa fa-trash"></i></button>
                        </form>
                    `},
                ],
            });
        });
    </script>
@endpush
