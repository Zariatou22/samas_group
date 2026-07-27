<nav class="navbar header-navbar pcoded-header">
    <div class="navbar-wrapper">
        <div class="navbar-logo">
            <a class="mobile-menu" id="mobile-collapse" href="#">
                <i class="ti-menu"></i>
            </a>
            <a href="{{ route('admin.home') }}" class="b-brand">
                <img src="{{ asset('images/logo.png') }}" class="img-fluid" alt="SAMAS Groupe" style="height: 2.5rem;">
            </a>
        </div>

        <div class="navbar-container container-fluid">
            <ul class="nav-left">
                <li>
                    <div class="sidebar_toggle">
                        <a href="#"><i class="icon-close icons"></i></a>
                    </div>
                </li>
            </ul>

            <ul class="nav-right">
                <li class="user-profile header-notification">
                    <div class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer;">
                        <span>{{ auth()->user()?->displayName() }}</span>
                        <i class="ti-angle-down"></i>
                    </div>
                    <ul class="show-notification profile-notification dropdown-menu dropdown-menu-right">
                        <li>
                            <a href="{{ route('profile.edit') }}"><i class="ti-user"></i> Mon profil</a>
                        </li>
                        <li>
                            <a href="{{ config('app.url') }}" target="_blank"><i class="ti-world"></i> Voir le site</a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item" style="border: none; background: none; width: 100%; text-align: left;">
                                    <i class="ti-power-off"></i> Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
