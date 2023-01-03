<header id="header" class="header-menu border-bottom user-select-none">
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
<div id="nav-bar" class="l-navbar user-select-none">
    <nav class="nav">
        <div>
            <div class="nav_list accordion">
                <a href="home" class="nav_link {{ setActive('home.index') }} rounded my-2">
                    <i class="fa-solid fa-house-chimney nav-icon fs-5 text-center" style="margin-left: -5px;"></i>
                    <span class="nav-name">{{ config('app.name', 'Laravel') }}</span>
                </a>

                <hr class="border-light p-0 m-0 mb-2">

                @foreach (Auth::user()->getMenusPerfil() as $menu)
                    <a
                        @isset($menu->url) href="{{ route($menu->url) }}" @endisset
                        title="{{$menu->nombre}}"
                        data-toggle="tooltip"
                        data-bs-placement="right"
                        @isset($menu->submenu)
                            data-bs-toggle="collapse"
                            data-bs-target="#__menu_{{ $menu->id_menu }}"
                        @endisset
                        class="nav_link {{ setActive($menu->url) }} rounded {{ !isset($menu->submenu) ? 'my-1' : 'my-0 btn show-more' }}"
                    >
                        <i class="{{ $menu->icon }} fs-5 text-center"></i>
                        <span class="nav_name">{{$menu->nombre}}</span>
                        {!! !isset($menu->url) ? '<i class="text-center fs-6 submenu_icon fa-solid fa-angle-down" style="margin-left: -6px;"></i>' : '' !!}
                    </a>
                    @isset($menu->submenu)
                        <ul class="list-group border-top-0 rounded-0 rounded-bottom collapse" id="__menu_{{ $menu->id_menu }}" data-bs-parent=".accordion">
                            @foreach ($menu->submenu as $submenu)
                                <li>
                                    <a
                                        @isset($submenu->url) href="{{ route($submenu->url) }}" @endisset
                                        title="{{$submenu->nombre}}"
                                        data-toggle="tooltip"
                                        data-bs-placement="right"
                                        data-menu="__menu_{{ $menu->id_menu }}"
                                        class="nav_link {{ setActive($submenu->url) }} rounded-bottom m-0">
                                        <i class="{{ $submenu->icon }} text-center"></i>
                                        <span class="nav_name">{{$submenu->nombre}}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endisset
                @endforeach
            </div>
        </div>
</div>
<script type="application/javascript">
    if(localStorage.getItem('menu-toggle') === 'true' && window.innerWidth >= 768) {
        document.getElementById('nav-bar').classList.add('show-panel');
        document.getElementById('header-toggle').classList.add('fa-xmark');
        document.getElementById('header').classList.add('body-pd');
        document.getElementById('body-pd').classList.add('body-pd')
    }
</script>
<div id="container">
    @yield('content')
</div>