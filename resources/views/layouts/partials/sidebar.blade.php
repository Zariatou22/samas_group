@php
    $user = auth()->user();

    $containersOpen = request()->routeIs('admin.bls.*', 'admin.product-types.*');
    $customersOpen = request()->routeIs('admin.customers.*');
    $authorizationsOpen = request()->routeIs('admin.authorizations.*');
    $providersOpen = request()->routeIs('admin.sources.*', 'admin.companies.*');
    $loadingsOpen = request()->routeIs('admin.loadings.*', 'admin.cars.*', 'admin.car-owners.*', 'admin.car-drivers.*');
    $t1Open = request()->routeIs('admin.loading-t1s.*') || (request()->routeIs('admin.loadings.index') && request('activeTab') === 'without_t1');
    $invoicesOpen = request()->routeIs('admin.mandataire-balances.*', 'admin.invoices.*', 'admin.invoice-labels.*', 'admin.accounting-invoices.*', 'admin.accounting-invoice-labels.*', 'admin.accounting-invoice-field-regulars.*', 'admin.invoice-advances.*', 'admin.proforma-invoices.*');
    $usersOpen = request()->routeIs('admin.users.*', 'admin.groups.*', 'admin.perms.*', 'admin.user-actions.*');
    $settingsOpen = request()->routeIs('admin.settings.*');
@endphp
<aside id="sidebar-left" class="sidebar-left">
    <div class="sidebar-header">
        <div class="sidebar-title">
            Navigation
        </div>
        <div class="sidebar-toggle d-none d-md-block" data-toggle-class="sidebar-left-collapsed"
            data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>
    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
                <ul class="nav nav-main">
                    <li class="{{ request()->routeIs('admin.home') ? 'nav-active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.home') }}">
                            <i class="fas fa-home" aria-hidden="true"></i>
                            <span>Accueil</span>
                        </a>
                    </li>
                    <li class="{{ request()->routeIs('admin.dashboard.index') ? 'nav-active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard.index') }}">
                            <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                            <span>Tableau de bord</span>
                        </a>
                    </li>

                    @if ($user?->canAccessModule())
                        <li class="nav-parent{{ $containersOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cubes" aria-hidden="true"></i>
                                <span>Conteneurs</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.bls.index') && request('activeTab', 'waiting') === 'all' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.bls.index', ['activeTab' => 'all']) }}">
                                        <span>Tous les BLS</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.bls.index') && request('activeTab', 'waiting') === 'waiting' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.bls.index', ['activeTab' => 'waiting']) }}">
                                        <span>B/L en attente</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.bls.index') && request('activeTab') === 'arrived' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.bls.index', ['activeTab' => 'arrived']) }}">
                                        <span>B/L Arrivé</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.bls.index') && request('activeTab') === 'ongoing' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.bls.index', ['activeTab' => 'ongoing']) }}">
                                        <span>En cours d'opération</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.bls.index') && request('activeTab') === 'completed' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.bls.index', ['activeTab' => 'completed']) }}">
                                        <span>B/L clôturé</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.bls.create') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.bls.create') }}">
                                        <span>Nouvel arrivage</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.product-types.index') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.product-types.index') }}">
                                        <span>Type d'emballage</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if ($user?->hasAccessLevel('Rédaction'))
                        <li class="nav-parent{{ $customersOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-user" aria-hidden="true"></i>
                                <span>Clients et mandataires</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.customers.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.customers.index') }}">
                                        <span>Mandataires</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-parent{{ $authorizationsOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-stamp" aria-hidden="true"></i>
                                <span>Déclarations</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.authorizations.index') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.authorizations.index') }}">
                                        <span>Liste des déclarations</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.authorizations.create') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.authorizations.create') }}">
                                        <span>Nouvelle déclaration</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-parent{{ $providersOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-handshake" aria-hidden="true"></i>
                                <span>Prestataires</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.sources.index') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.sources.index') }}">
                                        <span>Lieu d'enlèvement</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.companies.index') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.companies.index') }}">
                                        <span>Compagnies de shipping</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if ($user?->canAccessModule())
                        <li class="nav-parent{{ $loadingsOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-truck" aria-hidden="true"></i>
                                <span>Chargements</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.loadings.index') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.loadings.index') }}">
                                        <span>Tous les chargements</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.loadings.create') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.loadings.create') }}">
                                        <span>Charger</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.loadings.unloadings') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.loadings.unloadings') }}">
                                        <span>Tous les dépotages</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.cars.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.cars.index') }}">
                                        <span>Liste des véhicules</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.car-owners.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.car-owners.index') }}">
                                        <span>Transporteurs</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.car-drivers.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.car-drivers.index') }}">
                                        <span>Chauffeurs</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-parent{{ $t1Open ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-scroll" aria-hidden="true"></i>
                                <span>T1</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.loading-t1s.index') && request('activeTab', 'ongoing') === 'ongoing' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'ongoing']) }}">
                                        <span>T1 en cours de validité</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.loading-t1s.index') && request('activeTab') === 'expired' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.loading-t1s.index', ['activeTab' => 'expired']) }}">
                                        <span>T1 expiré</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.loadings.index') && request('activeTab') === 'without_t1' ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.loadings.index', ['activeTab' => 'without_t1']) }}">
                                        <span>Chargement en attente de T1</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if ($user?->canAccessModule())
                        <li class="nav-parent{{ $invoicesOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-receipt" aria-hidden="true"></i>
                                <span>Factures</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.mandataire-balances.*') || request()->routeIs('admin.invoices.*') || request()->routeIs('admin.invoice-labels.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.mandataire-balances.index') }}">
                                        <span>Factures prestataires</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.accounting-invoices.*') || request()->routeIs('admin.accounting-invoice-labels.*') || request()->routeIs('admin.accounting-invoice-field-regulars.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.accounting-invoices.index') }}">
                                        <span>Factures clients</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.proforma-invoices.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.proforma-invoices.index') }}">
                                        <span>Facture Pro Forma</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if ($user?->hasAccessLevel('Edition'))
                        <li class="nav-parent{{ $usersOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-users" aria-hidden="true"></i>
                                <span>Gestion des utilisateurs</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.users.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.users.index') }}">
                                        <span>Tous les utilisateurs</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.groups.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.groups.index') }}">
                                        <span>Groupes</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.perms.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.perms.index') }}">
                                        <span>Rôles et permissions</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.user-actions.*') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.user-actions.index') }}">
                                        <span>Journal des connexions</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    @if ($user?->hasAccessLevel('Administration'))
                        <li class="nav-parent{{ $settingsOpen ? ' nav-expanded nav-active' : '' }}">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog" aria-hidden="true"></i>
                                <span>Paramètres</span>
                            </a>
                            <ul class="nav nav-children">
                                <li{!! request()->routeIs('admin.settings.general') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.settings.general') }}">
                                        <span>Paramètres généraux</span>
                                    </a>
                                </li>
                                <li{!! request()->routeIs('admin.settings.email') ? ' class="nav-active"' : '' !!}>
                                    <a class="nav-link" href="{{ route('admin.settings.email') }}">
                                        <span>Paramètres email</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </nav>
        </div>
        <script>
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
    </div>
</aside>
