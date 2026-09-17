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
                    <tfoot class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">TOTAL</th>
                            <th></th>
                            <th></th>
                            <th class="text-center align-middle"></th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tfoot>
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
                footerCallback: function () {
                    const api = this.api();
                    const rowCount = api.rows({search: 'applied'}).count();
                    const totalCompanies = api.rows({search: 'applied'}).data().toArray()
                        .reduce((a, row) => a + (Array.isArray(row.companies) ? row.companies.length : 0), 0);
                    $(api.column(1).footer()).html(rowCount ? rowCount : '-');
                    $(api.column(3).footer()).html(totalCompanies ? totalCompanies : '-');
                },
                columns: [
                    {data: (d) => `<a href="/admin/customers/${d.id}/edit">${d.customer_name}</a>`},
                    {data: (d) => d.customer_contact ?? '-'},
                    {data: (d) => d.email ?? '-'},
                    {data: (d) => d.companies.length ? `<a href="/admin/customers/${d.id}/companies" class="btn btn-sm btn-outline-primary"><i class="fa fa-eye"></i> Voir</a>` : '-'},
                    {data: (d) => `<a href="/admin/customers/${d.id}/documents"><span class="badge badge-secondary">${d.documents_count}</span></a>`},
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
