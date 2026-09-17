<header class="header">
    <div class="logo-container">
        <a href="{{ route('admin.home') }}" class="logo">
            <img src="{{ asset('assets/images/logo-akadmin.png') }}" width="75" height="35" alt="AKADMIN" />
        </a>
        <div class="d-md-none toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html"
            data-fire-event="sidebar-left-opened">
            <i class="fas fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="header-right">
        <span class="separator"></span>

        <div id="userbox" class="userbox">
            <a href="#" data-toggle="dropdown">
                <figure class="profile-picture">
                    <img src="{{ asset('assets/images/avatar.png') }}" alt="{{ auth()->user()?->displayName() }}"
                        class="rounded-circle" data-lock-picture="{{ asset('assets/images/avatar.png') }}" />
                </figure>
                <div class="profile-info" data-lock-name="{{ auth()->user()?->displayName() }}">
                    <span class="name">{{ auth()->user()?->displayName() }}</span>
                </div>

                <i class="fa custom-caret"></i>
            </a>

            <div class="dropdown-menu">
                <ul class="list-unstyled mb-2">
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="{{ config('app.url') }}" target="_blank"><i
                                class="fas fa-external-link-alt"></i>Voir le site</a>
                    </li>
                    <li class="divider"></li>
                    <li>
                        <a role="menuitem" tabindex="-1" href="{{ route('profile.edit') }}"><i
                                class="fas fa-user"></i>Mon profil</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem" tabindex="-1" class="dropdown-item"
                                style="border: none; background: none; width: 100%; text-align: left; padding-left: 0;">
                                <i class="fas fa-power-off"></i>Déconnexion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
