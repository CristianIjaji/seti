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
                    data-title="Configurar impresora"
                    data-reload="false"
                    data-action={{ route('users.printer', Auth::user()->id_tercero) }}
                >
                    <i class="fa-solid fa-print"></i> Configurar impresora
                </a>
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
                {{-- @can('view', new App\Models\TblOrden)
                    <a href="{{ route('orden.index') }}" class="nav_link {{ setActive('orden.index') }} rounded my-2">
                        <i class="fa-solid fa-headset nav_icon"></i>
                        <span class="nav_name">Ordenes</span>
                    </a>    
                @endcan
                @can('view', new App\Models\TblHabitacion)
                    <a href="{{ route('rooms.index') }}" class="nav_link {{ setActive('rooms.index') }} rounded my-2">
                        <i class="fa-solid fa-bed nav_icon"></i>
                        <span class="nav_name">Habitaciones</span>
                    </a>
                @endcan --}}
                <a href="{{ route('sites.index') }}" class="nav_link {{ setActive('sites.index') }} rounded my-2">
                    <i class="fa-solid fa-house-flag nav_icon"></i>
                    <span class="nav_name">Estaciones</span>
                </a>
                <a href="{{ route('quotes.index') }}" class="nav_link {{ setActive('quotes.index') }} rounded my-2">
                    <i class="fa-solid fa-clipboard-list nav_icon"></i>
                    <span class="nav_name">Cotizaciones</span>
                </a>
                @can('view', new App\Models\TblTercero)
                    <a href="{{ route('clients.index') }}" class="nav_link {{ setActive('clients.index') }} rounded my-2">
                        <i class="fa-solid fa-address-book nav_icon"></i>
                        <span class="nav_name">Terceros</span>
                    </a>
                @endcan
                @can('view-user')
                    <a href="{{ route('users.index') }}" class="nav_link {{ setActive('users.index') }} rounded my-2">
                        <i class="fa-solid fa-chalkboard-user nav_icon"></i>
                        <span class="nav_name">Usuarios</span>
                    </a>
                @endcan
                @can('view', new App\Models\TblDominio)
                    <a href="{{ route('domains.index') }}" class="nav_link {{ setActive('domains.index') }} rounded my-2">
                        <i class="fa-solid fa-screwdriver nav_icon"></i>
                        <span class="nav_name">Dominios</span>
                    </a>
                @endcan
                @can('view', new App\Models\TblParametro)
                    <a href="{{ route('params.index') }}" class="nav_link {{ setActive('params.index') }} rounded my-2">
                        <i class="fa-solid fa-sliders nav_icon"></i>
                        <span class="nav_name">Parámetros</span>
                    </a>
                @endcan
            </div>
        </div>
    </nav>
</div>
<div id="container">
    @yield('content')
</div>