@extends('layouts.admin')

@section('title', 'Journal des connexions')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Journal des connexions (500 dernières)</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-bordered table-sm table-hover" id="userActionTable">
                    <thead class="thead-dark">
                        <tr>
                            <th class="text-center align-middle">UTILISATEUR</th>
                            <th class="text-center align-middle">ACTION</th>
                            <th class="text-center align-middle">PLATEFORME</th>
                            <th class="text-center align-middle">APPAREIL</th>
                            <th class="text-center align-middle">NAVIGATEUR</th>
                            <th class="text-center align-middle">IP</th>
                            <th class="text-center align-middle">DATE</th>
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
            $('#userActionTable').DataTable({
                ajax: '{{ route('admin.user-actions.data') }}',
                dataSrc: 'data',
                language: { url: '{{ asset('vendor/able/assets/json/datatable/fr-FR.json') }}' },
                order: [],
                columns: [
                    {data: (d) => d.user_name ?? '-'},
                    {data: (d) => d.action ?? '-'},
                    {data: (d) => d.platform ?? '-'},
                    {data: (d) => d.device ?? '-'},
                    {data: (d) => d.browser ?? '-'},
                    {data: (d) => d.ip ?? '-'},
                    {data: (d) => d.date_session ?? '-'},
                ],
            });
        });
    </script>
@endpush
