<!DOCTYPE html>
<html lang="fr" class="no-js fixed sidebar-left-collapsed sidebar-left-with-menu">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Accueil') - Administration</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/favicon/manifest.json') }}">
    <link rel="shortcut icon" href="{{ asset('assets/favicon/favicon.ico') }}" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light"
        rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datepicker3.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/skins/default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/skins/extension.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme-admin-extension.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fixedHeader.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/colReorder.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">

    @stack('styles')

    <script type="text/javascript" src="{{ asset('assets/js/modernizr.js') }}"></script>
</head>

<body>
    <div class="preloader">
        <div class="spinner"></div>
        <div class="spinner-2"></div>
    </div>

    <section class="body">
        @include('layouts.partials.header')

        <div class="inner-wrapper">
            @include('layouts.partials.sidebar')

            <section role="main" class="content-body">
                <header class="page-header">
                    <h2>@yield('title', 'Accueil')</h2>
                </header>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                @endif

                @yield('content')
            </section>
        </div>
    </section>

    <script type="text/javascript" src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery-confirm.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.browser.mobile.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap-datepicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/bootstrap-datepicker.fr.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/nanoscroller.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/ios7-switch.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.ui.touch-punch.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.appear.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/pnotify.custom.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/isotope.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/boxes.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/dataTables.fixedHeader.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/dataTables.colReorder.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/datatables.init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/theme.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/theme.init.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/utils.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/number.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/dialogs.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/admin.js') }}"></script>

    @stack('scripts')
</body>

</html>
