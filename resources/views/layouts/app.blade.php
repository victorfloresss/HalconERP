<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Halcón Materiales</title>

    <link rel="stylesheet" href="https://unpkg.com/@coreui/icons/css/all.min.css">

    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/favicon/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/favicon/favicon-96x96.png') }}">
    <meta name="theme-color" content="#ffffff">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    
    <div class="sidebar sidebar-dark sidebar-fixed border-end" id="sidebar">
        <div class="sidebar-header border-bottom">
            <div class="sidebar-brand">
                <strong>HALCÓN ERP</strong>
            </div>
            <button class="btn-close d-lg-none" type="button" data-coreui-theme="dark" aria-label="Close" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()"></button>
        </div>
        
        <ul class="sidebar-nav" data-coreui="navigation" data-simplebar>
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">
                    <i class="nav-icon cil-speedometer"></i> Dashboard
                </a>
            </li>

            <li class="nav-title">Operaciones</li>

            <li class="nav-item">
                <a class="nav-link {{ request()->is('orders') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                    <i class="nav-icon cil-list"></i> Lista de Pedidos
                </a>
            </li>

            @if(Auth::user()->role->slug == 'sales' || Auth::user()->role->slug == 'admin')
            <li class="nav-item">
                <a class="nav-link {{ request()->is('orders/create') ? 'active' : '' }}" href="{{ route('orders.create') }}">
                    <i class="nav-icon cil-plus"></i> Nuevo Pedido
                </a>
            </li>
            @endif

            @if(in_array(auth()->user()->role->slug, ['warehouse', 'purchasing', 'admin']))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('inventory.index') }}">
                        <i class="nav-icon cil-storage"></i> Inventario / Compras
                    </a>
                </li>
            @endif

            @if(Auth::user()->role->slug == 'admin')
            <li class="nav-title">Administración</li>
            
            <li class="nav-item">
                <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                    <i class="nav-icon cil-people"></i> Empleados
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-danger {{ request()->is('orders/trash') ? 'active' : '' }}" href="{{ route('orders.trash') }}">
                    <i class="nav-icon cil-trash"></i> Pedidos Eliminados
                </a>
            </li>
            @endif
        </ul>
        
        <div class="sidebar-footer border-top d-none d-md-flex">     
            <button class="sidebar-toggler" type="button" data-coreui-toggle="unfoldable"></button>
        </div>
    </div>

    <div class="wrapper d-flex flex-column min-vh-100 bg-light">
        
        <header class="header header-sticky p-0 mb-4">
            <div class="container-fluid border-bottom px-4">
                <button class="header-toggler" type="button" onclick="coreui.Sidebar.getInstance(document.querySelector('#sidebar')).toggle()" style="margin-inline-start: -14px;">
                    <i class="icon icon-lg cil-menu"></i>
                </button>
                
                <ul class="header-nav d-none d-lg-flex">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Inicio</a></li>
                </ul>

                <ul class="header-nav ms-auto">
                    @guest
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link py-0 pe-0 d-flex align-items-center" data-coreui-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                <span class="me-2">{{ Auth::user()->name }} ({{ Auth::user()->role->name }})</span>
                                <div class="avatar avatar-md">
                                    <img class="avatar-img" src="{{ asset('assets/img/avatars/8.jpg') }}" alt="{{ Auth::user()->email }}">
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end pt-0">
                                <div class="dropdown-header bg-body-tertiary text-body-secondary fw-semibold rounded-top mb-2">Cuenta</div>
                                
                                <a class="dropdown-item" href="#">
                                    <i class="icon me-2 cil-user"></i> Perfil
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="icon me-2 cil-account-logout"></i> Cerrar Sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </header>

        <div class="body flex-grow-1">
            <div class="container-lg px-4">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @yield('content')
            </div>
        </div>

        <footer class="footer px-4">
            <div><a href="{{ url('/') }}">Halcón Materiales</a> &copy; {{ date('Y') }}.</div>
        </footer>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', () => {
          const header = document.querySelector('header.header');
          document.addEventListener('scroll', () => {
            if (header) {
              header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
            }
          });
      });
    </script>

    <script>
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Acceso Denegado',
            text: "{{ session('error') }}",
            confirmButtonColor: '#3085d6',
        });
    @endif

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Logrado!',
            text: "{{ session('success') }}",
            timer: 2000,
            showConfirmButton: false
        });
    @endif
</script>

</body>
</html>