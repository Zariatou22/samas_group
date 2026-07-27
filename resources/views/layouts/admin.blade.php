<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Accueil') - SAMAS Groupe</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/pages/waves/css/waves.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/icon/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/css/jquery.mCustomScrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/css/style.css') }}">

    <style>
        /* Sous-menus pliables : le comportement natif du template (3D flip /
           flyout absolu) n'est utilisé que pour la sidebar réduite ; en mode
           déplié on affiche un accordéon simple, plus fiable que d'essayer
           d'adapter le système de classes/attributs du template. */
        #pcoded:not([vertical-nav-type="collapsed"]) .pcoded-navbar .pcoded-hasmenu > .pcoded-submenu {
            display: none;
            position: static;
            opacity: 1;
            visibility: visible;
            width: 100%;
            margin: 5px 0 0;
            transform: none;
        }
        #pcoded:not([vertical-nav-type="collapsed"]) .pcoded-navbar .pcoded-hasmenu.sidebar-open > .pcoded-submenu {
            display: block;
        }
        #pcoded:not([vertical-nav-type="collapsed"]) .pcoded-navbar .pcoded-hasmenu > a {
            cursor: pointer;
        }
        .pcoded-navbar .sidebar-caret {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            transition: transform 0.25s ease;
        }
        .pcoded-hasmenu.sidebar-open > a > .sidebar-caret {
            transform: translateY(-50%) rotate(90deg);
        }

        /* Effet de survol des liens de la sidebar (même esprit que les cartes
           de la page d'accueil : légère élévation + icône qui grossit). */
        .pcoded-navbar .pcoded-item > li > a,
        .pcoded-navbar .pcoded-submenu > li > a {
            transition: transform 0.2s ease-out, box-shadow 0.2s ease-out, background-color 0.2s ease-out;
        }
        .pcoded-navbar .pcoded-item > li > a:hover,
        .pcoded-navbar .pcoded-submenu > li > a:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
            border-radius: 6px;
        }
        .pcoded-navbar .pcoded-micon i {
            transition: transform 0.2s ease-out;
            display: inline-block;
        }
        .pcoded-navbar .pcoded-item > li > a:hover .pcoded-micon i {
            transform: scale(1.2);
        }

        /* Sous-menus : chevron au lieu d'une icône métier, aligné avec le
           texte des menus parents (même colonne que l'icône 30px + marge
           du niveau supérieur, cf. .pcoded-item > li > a > .pcoded-micon). */
        .pcoded-navbar .pcoded-submenu > li > a {
            display: flex;
            align-items: center;
            padding-left: 60px !important;
        }
        .pcoded-navbar .pcoded-submenu > li > a > .pcoded-submenu-caret {
            position: absolute;
            left: 22px;
            top: 50%;
            width: auto;
            height: auto;
            padding: 0;
            margin: 0;
            font-size: 10px;
            opacity: 0.5;
            transform: translateY(-50%);
        }
        .pcoded-navbar .pcoded-submenu > li > a:hover > .pcoded-submenu-caret,
        .pcoded-navbar .pcoded-submenu > li.active > a > .pcoded-submenu-caret {
            opacity: 1;
        }
        .pcoded-navbar .pcoded-submenu > li > a:hover .pcoded-submenu-caret i {
            transform: translateY(-2px);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div id="pcoded" class="pcoded">
        <div class="pcoded-overlay-box"></div>
        <div class="pcoded-container navbar-wrapper">
            @include('layouts.partials.header')

            <div class="pcoded-main-container">
                <div class="pcoded-wrapper">
                    @include('layouts.partials.sidebar')

                    <div class="pcoded-content">
                        <div class="page-header">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">@yield('title', 'Accueil')</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pcoded-inner-content">
                            <div class="main-body">
                                <div class="page-wrapper">
                                    <div class="page-body">
                                        @if (session('success'))
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                {{ session('success') }}
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                        @endif
                                        @if (session('error'))
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                {{ session('error') }}
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                        @endif

                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.vLayout = {
            NavbarBackground: 'themelight1',
            FixedHeaderPosition: true,
            collapseVerticalLeftHeader: true,
            onLayoutChange: { desktop: 'expanded', tablet: 'collapsed', phone: 'offcanvas' },
            onToggleVerticalMenu: { desktop: 'collapsed', tablet: 'expanded', phone: 'expanded' },
        };
    </script>

    <script src="{{ asset('vendor/able/assets/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/popper.js/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/pages/waves/js/waves.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/jquery-slimscroll/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/modernizr/modernizr.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/SmoothScroll.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/jquery.mCustomScrollbar.concat.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/moment.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/pcoded.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/vertical-layout.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/script.js') }}"></script>

    <script>
        // Persistance de l'état réduit/déplié de la sidebar entre les pages
        // (le template stock ne le fait pas — cf. plan).
        (function () {
            const KEY = 'sidebarCollapsed';
            const pcoded = document.getElementById('pcoded');

            function applyCollapsed(collapsed) {
                pcoded.setAttribute('vertical-nav-type', collapsed ? 'collapsed' : 'expanded');
            }

            applyCollapsed(localStorage.getItem(KEY) === '1');

            document.querySelectorAll('.sidebar_toggle a, #mobile-collapse').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    setTimeout(function () {
                        const isCollapsed = pcoded.getAttribute('vertical-nav-type') === 'collapsed';
                        localStorage.setItem(KEY, isCollapsed ? '1' : '0');
                    }, 50);
                });
            });
        })();

        // Pliage/dépliage des sous-menus de la sidebar (un seul groupe ouvert
        // à la fois) — la section correspondant à la page courante est déjà
        // ouverte par défaut via la classe "sidebar-open" côté serveur.
        (function () {
            document.querySelectorAll('.pcoded-navbar .pcoded-hasmenu > a').forEach(function (trigger) {
                trigger.addEventListener('click', function (e) {
                    e.preventDefault();
                    const parent = trigger.parentElement;
                    const wasOpen = parent.classList.contains('sidebar-open');

                    document.querySelectorAll('.pcoded-navbar .pcoded-hasmenu.sidebar-open').forEach(function (li) {
                        if (li !== parent) {
                            li.classList.remove('sidebar-open');
                        }
                    });

                    parent.classList.toggle('sidebar-open', !wasOpen);
                });
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
