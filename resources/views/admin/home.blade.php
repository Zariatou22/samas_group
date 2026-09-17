@extends('layouts.admin')

@section('title', 'Accueil')

@section('content')
    <div style="height: 1px; width: 100%; margin-bottom: -30px;"></div>
    <div class="d-flex align-items-center justify-content-center" style="min-height: calc(100vh - 260px);">
        <div class="row justify-content-center w-100">
            <div class="col-xl-3 col-lg-3 col-md-4">
                <div class="card mb-3 no-select click-to-run" role="button" data-href="{{ route('admin.operations') }}">
                    <div class="card-header bg-primary">
                        <div class="card-header-icon">
                            <i class="fas fa-dolly"></i>
                        </div>
                    </div>
                    <div class="card-body text-center bg-primary text-white p-0">
                        <div class="bg-dark" style="height: 50px; width: 100%; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px">
                            <h5 class="text-truncate font-weight-semibold mt-3 text-center" style="line-height: 50px">Opérations</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4">
                <div class="card mb-3 no-select click-to-run" role="button" data-href="{{ route('admin.tracking-menu') }}">
                    <div class="card-header bg-warning">
                        <div class="card-header-icon">
                            <i class="fas fa-route"></i>
                        </div>
                    </div>
                    <div class="card-body text-center bg-warning text-white p-0">
                        <div class="bg-dark" style="height: 50px; width: 100%; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px">
                            <h5 class="text-truncate font-weight-semibold mt-3 text-center" style="line-height: 50px">Suivi et contrôle</h5>
                        </div>
                    </div>
                </div>
            </div>
            @if (auth()->user()?->hasAccessLevel('Edition'))
                <div class="col-xl-3 col-lg-3 col-md-4">
                    <div class="card mb-3 no-select click-to-run" role="button" data-href="{{ route('admin.accounting') }}">
                        <div class="card-header bg-success">
                            <div class="card-header-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                        <div class="card-body text-center bg-success text-white p-0">
                            <div class="bg-dark" style="height: 50px; width: 100%; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px">
                                <h5 class="text-truncate font-weight-semibold mt-3 text-center" style="line-height: 50px">Comptabilité</h5>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="col-xl-3 col-lg-3 col-md-4">
                <div class="card mb-3 no-select click-to-run" role="button" data-href="{{ route('admin.document-menu') }}">
                    <div class="card-header bg-dark">
                        <div class="card-header-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                    </div>
                    <div class="card-body text-center bg-dark text-white p-0">
                        <div class="bg-dark" style="height: 50px; width: 100%; border-bottom-left-radius: 5px; border-bottom-right-radius: 5px">
                            <h5 class="text-truncate font-weight-semibold mt-3 text-center" style="line-height: 50px">Document</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
