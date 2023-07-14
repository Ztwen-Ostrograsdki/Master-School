<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{-- <meta name="caffeinated" content="false"> --}}

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        @if(!App\Helpers\RouteManager::hasUrl('administration'))
            @include("components.mycomponents.styles") {{-- chargement des styles --}}
        @else
            @include("components.mycomponents.adminStyles") {{-- chargement des styles --}}
        @endif
        

        <!-- Scripts -->


    </head>
       {{-- chargement des modals --}}
        @livewire('registering-new-user')
        @livewire('forgot-password')
        @livewire('login-user')
        @auth 
            @livewire('logout') 
            @livewire('user-profil-manager') 
            @livewire('default-modals') 
        @endauth
        
        {{-- chargement des modals --}}
    @if(!App\Helpers\RouteManager::hasUrl('administration'))
        <body class="font-sans antialiased" style="background-color: lightslategray;">
            @include('components.mycomponents.loader') {{-- chargement du loader --}}
            @livewire('header') {{-- chargement du header --}}
            <div class=" bg-gray-100 border" style="background-color: rgb(167, 193, 219); min-height: 150vh !important;">
                <!-- Page Content -->
                <div class="">
                    @if (isset($slot))
                        <div class="">
                            {{ $slot }}
                        </div>
                    @else
                        {{abort(404, "La Page que vous rechercher n'existe pas!!!")}}
                    @endif
                </div>
            </div>
            @include('sweetalert::alert')
            @if(Route::currentRouteName() !== 'email-verification-notify' && Route::currentRouteName() !== 'force-email-verification-notify' && Route::currentRouteName() !== 'messenger')
                @livewire("footer") {{-- chargement du footer --}}
            @endif
            <script src="//{{Request::getHost()}}:6001/socket.io/socket.io.js" ></script>
            <script src="{{ mix('js/app.js') }}" defer></script>
            <script>
            </script>
            @include("components.mycomponents.scripts") {{-- chargement des scripts js --}}
        </body>
    @else
        <body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
        <div class=" bg-gray-100 border" style="background-color: rgb(167, 193, 219); min-height: 150vh !important;">
            <!-- Page Content -->
            <div class="">

                <div class="m-0 p-0 w-100" >              
                    <div class="wrapper">
                        <!-- Preloader -->
                        <div class="preloader flex-column justify-content-center align-items-center">
                            <img class="animation__wobble" src="/myassets/dist/img/AdminLTELogo.png" alt="SchoolLogo" height="60" width="60">
                        </div>
                        @livewire('admin-header-component')
                        @livewire('admin-aside-component')
                        @livewire('admin-modals')
                        <div class="content-wrapper">
                            <div class="content-header">
                                <div class="container-fluid">
                                <div class="row mb-2">
                                    <div class="col-sm-6">
                                        <div class="d-flex">
                                            @livewire('school-years-manager')
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        @php 
                                            setlocale(LC_TIME, "fr_FR.utf8", 'fra');

                                            $date = strftime("%A %d %B %Y", time());

                                            $time = strftime("%H H %M' ", time());

                                        @endphp
                                        <ol class="breadcrumb float-sm-right">
                                            @isRoute('admin')
                                                <li class="breadcrumb-item"><a href="{{route('admin')}}">Administration</a></li>
                                                <li class="breadcrumb-item active">Tableau de bord</li>
                                                <li class="breadcrumb-item active">
                                                    <span class="bi-calendar"></span>
                                                    {{ ucwords($date) }}
                                                    <span>
                                                        |
                                                        <span class="bi-clock mx-1"></span>
                                                        {{ $time }}
                                                    </span>
                                                </li>
                                            @else
                                                <li class="breadcrumb-item active text-warning">
                                                    <span class="bi-calendar"></span>
                                                    {{ ucwords($date) }}
                                                    <span>
                                                        |
                                                        <span class="bi-clock mx-1"></span>
                                                        {{ $time }}
                                                    </span>
                                                </li>
                                                @isRoute('classe_profil')
                                                    @if(session()->has('classe_selected') && session('classe_selected'))
                                                        <li class="breadcrumb-item active">{{session('classe_selected')}}</li>
                                                    @else
                                                    <li class="breadcrumb-item active">{{"Une classe"}}</li>
                                                    @endif
                                                @endisRoute
                                            @endisRoute
                                        </ol>
                                    </div>
                                </div>
                                </div>
                            </div>
                            @if (isset($slot))
                            <div class="">
                                {{ $slot }}
                            </div>
                            @else
                                {{abort(404, "La Page que vous rechercher n'existe pas!!!")}}
                            @endif
                        </div>
                        @livewire('admin-customiser-component')
                    </div>
                </div>
                
            </div>
        </div>
        @include('sweetalert::alert')
        <script src="//{{Request::getHost()}}:6001/socket.io/socket.io.js" ></script>
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script>
        </script>
        @include("components.mycomponents.adminScripts") {{-- chargement des styles --}}
    </body>
    @endif
    
</html>
