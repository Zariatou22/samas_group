@php
    $user = auth()->user();

    $containersOpen = request()->routeIs('admin.bls.*', 'admin.product-types.*');
    $customersOpen = request()->routeIs('admin.customers.*');
    $authorizationsOpen = request()->routeIs('admin.authorizations.*');
    $providersOpen = request()->routeIs('admin.sources.*', 'admin.companies.*');
    $loadingsOpen = request()->routeIs('admin.loadings.*', 'admin.cars.*', 'admin.car-owners.*', 'admin.car-drivers.*');
    $t1Open = request()->routeIs('admin.loading-t1s.*') || (request()->routeIs('admin.loadings.index') && request('activeTab') === 'without_t1');
    $invoicesOpen = request()->routeIs('admin.mandataire-balances.*', 'admin.invoices.*', 'admin.invoice-labels.*', 'admin.accounting-invoices.*', 'admin.accounting-invoice-labels.*', 'admin.accounting-invoice-field-regulars.*', 'admin.invoice-advances.*');
    $usersOpen = request()->routeIs('admin.users.*', 'admin.groups.*', 'admin.perms.*', 'admin.user-actions.*');
    $settingsOpen = request()->routeIs('admin.settings.*');
@endphp
<nav class="pcoded-navbar">
    <div class="sidebar_toggle"><a href="#"><i class="icon-close icons"></i></a></div>
    <div class="pcoded-inner-navbar main-menu">
        <ul class="pcoded-item pcoded-left-item">
            <li class="{{ request()->routeIs('admin.home') ? 'active' : '' }}">
                <a href="{{ route('admin.home') }}" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="ti-home"></i></span>
                    <span class="pcoded-mtext">Accueil</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.dashboard.index') ? 'active' : '' }}">
                <a href="{{ route('admin.dashboard.index') }}" class="waves-effect waves-dark">
                    <span class="pcoded-micon"><i class="ti-dashboard"></i></span>
                    <span class="pcoded-mtext">Tableau de bord</span>
                </a>
            </li>
        </ul>

        @if ($user?->canAccessModule())
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ $containersOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-layers"></i></span>
                        <span class="pcoded-mtext">Conteneurs</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.bls.index') && request('activeTab', 'waiting') === 'all' ? 'active' : '' }}">
                            <a href="{{ route('admin.bls.index', ['activeTab' => 'all']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Tous les BLS</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.bls.index') && request('activeTab', 'waiting') === 'waiting' ? 'active' : '' }}">
                            <a href="{{ route('admin.bls.index', ['activeTab' => 'waiting']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">B/L en attente</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.bls.index') && request('activeTab') === 'arrived' ? 'active' : '' }}">
                            <a href="{{ route('admin.bls.index', ['activeTab' => 'arrived']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">B/L Arrivé</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.bls.index') && request('activeTab') === 'ongoing' ? 'active' : '' }}">
                            <a href="{{ route('admin.bls.index', ['activeTab' => 'ongoing']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">En cours d'opération</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.bls.index') && request('activeTab') === 'completed' ? 'active' : '' }}">
                            <a href="{{ route('admin.bls.index', ['activeTab' => 'completed']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">B/L clôturé</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.bls.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.bls.create') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Nouvel arrivage</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.product-types.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.product-types.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Type d'emballage</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        @if ($user?->hasAccessLevel('Rédaction'))
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ $customersOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-id-badge"></i></span>
                        <span class="pcoded-mtext">Clients et mandataires</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.customers.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Mandataires</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="pcoded-hasmenu {{ $authorizationsOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-folder"></i></span>
                        <span class="pcoded-mtext">Déclarations</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.authorizations.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.authorizations.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Liste des déclarations</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.authorizations.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.authorizations.create') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Nouvelle déclaration</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="pcoded-hasmenu {{ $providersOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-briefcase"></i></span>
                        <span class="pcoded-mtext">Prestataires</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.sources.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.sources.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Lieu d'enlèvement</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.companies.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.companies.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Compagnies de shipping</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        @if ($user?->canAccessModule())
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ $loadingsOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-truck"></i></span>
                        <span class="pcoded-mtext">Chargement</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.loadings.index') ? 'active' : '' }}">
                            <a href="{{ route('admin.loadings.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Tous les chargements</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.loadings.create') ? 'active' : '' }}">
                            <a href="{{ route('admin.loadings.create') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Charger</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.cars.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Liste des véhicules</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.car-owners.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.car-owners.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Transporteurs</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.car-drivers.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.car-drivers.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Chauffeurs</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="pcoded-hasmenu {{ $t1Open ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-flag"></i></span>
                        <span class="pcoded-mtext">T1</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.loading-t1s.index') && request('activeTab', 'ongoing') === 'ongoing' ? 'active' : '' }}">
                            <a href="{{ route('admin.loading-t1s.index', ['activeTab' => 'ongoing']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">T1 en cours de validité</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.loading-t1s.index') && request('activeTab') === 'expired' ? 'active' : '' }}">
                            <a href="{{ route('admin.loading-t1s.index', ['activeTab' => 'expired']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">T1 expiré</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.loadings.index') && request('activeTab') === 'without_t1' ? 'active' : '' }}">
                            <a href="{{ route('admin.loadings.index', ['activeTab' => 'without_t1']) }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Chargement en attente de T1</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        @if ($user?->canAccessModule())
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ $invoicesOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-wallet"></i></span>
                        <span class="pcoded-mtext">Facture</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.mandataire-balances.*') || request()->routeIs('admin.invoices.*') || request()->routeIs('admin.invoice-labels.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.mandataire-balances.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Factures prestataires</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.accounting-invoices.*') || request()->routeIs('admin.accounting-invoice-labels.*') || request()->routeIs('admin.accounting-invoice-field-regulars.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.accounting-invoices.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Factures clients</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.invoice-advances.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.invoice-advances.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Reçus d'avance transport</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        @if ($user?->hasAccessLevel('Edition'))
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ $usersOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-lock"></i></span>
                        <span class="pcoded-mtext">Gestion des utilisateurs</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.users.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Tous les utilisateurs</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.groups.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.groups.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Groupes</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.perms.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.perms.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Rôles et permissions</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.user-actions.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.user-actions.index') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Journal des connexions</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        @if ($user?->hasAccessLevel('Administration'))
            <ul class="pcoded-item pcoded-left-item">
                <li class="pcoded-hasmenu {{ $settingsOpen ? 'sidebar-open active' : '' }}">
                    <a href="#!" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="ti-settings"></i></span>
                        <span class="pcoded-mtext">Paramètres</span>
                        <i class="fa fa-angle-right sidebar-caret"></i>
                    </a>
                    <ul class="pcoded-submenu">
                        <li class="{{ request()->routeIs('admin.settings.general') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings.general') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Paramètres généraux</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.settings.email') ? 'active' : '' }}">
                            <a href="{{ route('admin.settings.email') }}" class="waves-effect waves-dark">
                                <span class="pcoded-micon pcoded-submenu-caret"><i class="fa fa-chevron-up"></i></span>
                                <span class="pcoded-mtext">Paramètres email</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif
    </div>
</nav>
