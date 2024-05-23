<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema De Prestamos</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm ">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Sistema de Prestamos V1.0  <svg enable-background="new 0 0 64 64" height="30px" id="Layer_1" version="1.1" viewBox="0 0 64 64" width="30px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="financial_report"><g id="training"><g><rect fill="#E0E0E0" height="37" width="54" x="5" y="12"/></g><g opacity="0.2"><rect fill="#37474F" height="2" width="54" x="5" y="12"/></g><g><g><path d="M61,11c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h54C60.1,9,61,9.9,61,11L61,11z" fill="#90A4AE"/></g><g><path d="M3.279,10h57.441C60.372,9.406,59.734,9,59,9H5C4.266,9,3.627,9.406,3.279,10z" fill="#B0BEC5"/></g><g><path d="M3.279,12C3.627,12.594,4.266,13,5,13h54c0.734,0,1.372-0.406,1.721-1H3.279z" fill="#78909C"/></g></g><g><g><path d="M61,50c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2l0,0c0-1.1,0.9-2,2-2h54C60.1,48,61,48.9,61,50L61,50z" fill="#90A4AE"/></g><g><path d="M3.279,49h57.441c-0.349-0.594-0.986-1-1.721-1H5C4.266,48,3.627,48.406,3.279,49z" fill="#B0BEC5"/></g><g><path d="M3.279,51C3.627,51.594,4.266,52,5,52h54c0.734,0,1.372-0.406,1.721-1H3.279z" fill="#78909C"/></g></g><g><path d="M30,9V6c0-1.104,0.896-2,2-2l0,0c1.104,0,2,0.896,2,2v3H30z" fill="#607D8B"/></g><g><polygon fill="#607D8B" points="45,61 49,61 46,52 42,52    "/></g><g><polygon fill="#607D8B" points="19,61 15,61 18,52 22,52    "/></g><g opacity="0.2"><polygon fill="#37474F" points="21.333,54 22,52 18,52 17.333,54    "/></g><g opacity="0.2"><polygon fill="#37474F" points="46.667,54 46,52 42,52 42.667,54    "/></g><g><rect fill="#546E7A" height="1" width="8" x="10" y="20"/></g><g><rect fill="#546E7A" height="1" width="8" x="10" y="17"/></g><g><rect fill="#FF7043" height="8" width="4" x="11" y="34"/></g><g><rect fill="#FF7043" height="14" width="4" x="18" y="28"/></g><g><rect fill="#FF7043" height="20" width="4" x="25" y="22"/></g><g><rect fill="#546E7A" height="1" width="20" x="10" y="43"/></g><g><rect fill="#546E7A" height="1" width="8" x="35" y="42"/></g><g><rect fill="#546E7A" height="1" width="19" x="35" y="38"/></g><g><rect fill="#546E7A" height="1" width="19" x="35" y="34"/></g><g><rect fill="#546E7A" height="1" width="19" x="35" y="30"/></g></g><polygon fill="#FF7043" points="54.394,19.365 54,17 51.635,17.395 50.798,18.565 52.465,18.287 48.891,23.293 44.912,20.31    40.862,25.372 36.829,23.355 34.584,26.723 35.416,27.277 37.171,24.645 41.138,26.628 45.088,21.69 49.109,24.707 53.28,18.868    53.558,20.536  "/></g></svg>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            <!-- @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Registrar') }}</a>
                                </li>
                            @endif -->
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                <i class="fas fa-user"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Cerrar Session') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>

                             <!-- Mostrar enlace de registro solo para el administrador -->
                             @auth
                                 @if (auth()->user()->esAdmin())
                                      @if (Route::has('register'))
                                         <li class="nav-item">
                                             <a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-circle"></i> {{ __('Usuarios') }}</a>
                                         </li>
                                      @endif
                                @endif
                          @endauth
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
