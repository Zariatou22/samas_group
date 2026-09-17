@extends('layouts.admin')

@section('title', 'Factures pro forma')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.proforma-invoices.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvelle facture pro forma</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des factures pro forma</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="proformaInvoiceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">R&Eacute;F&Eacute;RENCE</th>
                            <th class="text-center align-middle">CLIENT</th>
                            <th class="text-center align-middle">DATE</th>
                            <th class="text-center align-middle">MAISON CONSIGNATAIRE</th>
                            <th class="text-center align-middle">CONTENEURS</th>
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
            $('#proformaInvoiceTable').DataTable({
                ajax: '{{ route('admin.proforma-invoices.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/proforma-invoices/${d.id}/edit">${d.reference}</a>`},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.date_issued ?? '-'},
                    {data: (d) => d.consignee_house ?? '-'},
                    {data: (d) => [d.container_type, d.container_count].filter(Boolean).join(' x ') || '-'},
                    {data: (d) => `
                        <a href="/admin/proforma-invoices/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/proforma-invoices/${d.id}" class="d-inline" onsubmit="return confirm('Archiver cette facture pro forma ?')">
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
