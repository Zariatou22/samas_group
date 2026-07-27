@extends('layouts.admin')

@section('title', 'Factures prestataires')

@section('content')
    <p>
        <a href="{{ route('admin.mandataire-balances.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Nouvelle facture</a>
        <a href="{{ route('admin.invoice-labels.index') }}" class="btn btn-outline-secondary"><i class="fa fa-tags"></i> Libellés</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Liste des factures{{ $customer ? ' — mandataire filtré' : '' }}</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="invoiceTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">R&Eacute;F&Eacute;RENCE</th>
                            <th class="text-center align-middle">N&deg; B/L</th>
                            <th class="text-center align-middle">MANDATAIRE</th>
                            <th class="text-center align-middle">D&Eacute;SIGNATION</th>
                            <th class="text-center align-middle">MONTANT</th>
                            <th class="text-center align-middle">PAY&Eacute;E</th>
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
            $('#invoiceTable').DataTable({
                ajax: '{{ route('admin.invoices.data', ['customer' => $customer]) }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                columns: [
                    {data: (d) => `<a href="/admin/invoices/${d.id}/edit">${d.reference}</a>`},
                    {data: (d) => d.bl_id ? `<a href="/admin/invoices/bl/${d.bl_id}">${d.bl}</a>` : (d.bl ?? '-')},
                    {data: (d) => d.customer_name ?? '-'},
                    {data: (d) => d.label_name ?? '-'},
                    {data: (d) => Number(d.amount ?? 0).toFixed(2)},
                    {data: (d) => d.paid ? '<span class="badge badge-success">Oui</span>' : '<span class="badge badge-secondary">Non</span>'},
                    {data: (d) => `
                        <a href="/admin/invoices/${d.id}/edit" class="btn btn-sm btn-primary" title="Modifier"><i class="fa fa-edit"></i></a>
                        <form method="POST" action="/admin/invoices/${d.id}" class="d-inline" onsubmit="return confirm('Archiver cette facture ?')">
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
