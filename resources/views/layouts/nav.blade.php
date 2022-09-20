<header class="header-menu border-bottom" id="header">
{{-- <header class="fixed-top d-flex justify-content-between align-items-center border" id="header"> --}}
    <div class="header_toggle">
        <i class="fa-solid fa-bars" id="header-toggle"></i>
    </div>
    <li class="dropdown d-flex justify-content-center">
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
</header>
<div class="l-navbar" id="nav-bar">
    <nav class="nav">
        <div>
            <a href="home" class="nav_logo {{ request()->routeIs('home.index') ? 'active rounded' : '' }}">
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