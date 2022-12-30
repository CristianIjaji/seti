<header class="header-menu border-bottom" id="header">
    <div class="header_toggle">
        <i class="fa-solid fa-bars" id="header-toggle"></i>
    </div>
    <div class="d-flex justify-content-center align-items-center">
        <li class="dropdown list-group">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                @isset(Auth::user()->usuario)
                    {{ Auth::user()->usuario }}
                @endisset
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li>
                    <a class="dropdown-item pl-3" href="{{ route('logout') }}" onclick="event.preventDefault();
                        closeConnection();
                        document.getElementById('logout-form').submit();">
                        <i class="fas fa-power-off"></i> {{ __('Logout') }}
                    </a>
            
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
                <li>
                    <a
                        class="dropdown-item ps-3 modal-form" href="#"
                        data-toggle="modal-md"
                        data-title="Cambiar contraseña"
                        data-reload="false"
                        data-action={{ route('users.password', Auth::user()->id_tercero) }}
                    >
                        <i class="fa-solid fa-key"></i> Cambiar contraseña
                    </a>
                </li>
            </ul>
        </li>

        <li class="dropdown list-group ps-3">
            <button class="btn btn-link nav-link py-2 px-2 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static">
                <svg class="bi my-1 theme-icon-active"><use href="#moon-stars-fill"></use></svg>
                {{-- <span class="d-lg-none ms-2">Cambiar tema</span> --}}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme" style="--bs-dropdown-min-width: 8rem;">
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light">
                        <svg class="bi me-2 opacity-50 theme-icon"><use href="#sun-fill"></use></svg>
                        Light
                        <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                    </button>
                </li>
                <li>
                    <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="dark">
                        <svg class="bi me-2 opacity-50 theme-icon"><use href="#moon-stars-fill"></use></svg>
                        Dark
                        <svg class="bi ms-auto d-none"><use href="#check2"></use></svg>
                    </button>
                </li>
            </ul>
        </li>
    </div>
</header>
<div class="l-navbar" id="nav-bar">
    <nav class="nav">
        <div>
            <a href="home" class="nav_logo {{ request()->routeIs('home.index') ? 'active rounded my-2' : '' }}">
                <i class="fa-solid fa-house-chimney nav_logo-icon {{ request()->routeIs('home.index') ? 'text-primary' : 'text-white' }}" style="margin-left: -5px;"></i>
                <span class="nav_logo-name {{ request()->routeIs('home.index') ? 'text-primary' : 'text-white' }}">{{ config('app.name', 'Laravel') }}</span>
            </a>
            <hr class="bg-white">
            <div class="nav_list">
                @foreach (Auth::user()->getMenusPerfil() as $menu)
                    <a href="{{ route($menu->url) }}" title="{{$menu->nombre}}" data-toggle="tooltip" data-bs-placement="right" class="nav_link {{ setActive($menu->url) }} rounded my-2">
                        <i class="{{ $menu->icon }} fs-5 text-center"></i>
                        <span class="nav_name">{{$menu->nombre}}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>
</div>
<div id="container">
    @yield('content')
</div>