<div>
    <header class="d-lg-block d-xxl-block d-xl-block d-none yours @fixedHeaderForRoute() position-fixed @endfixedHeaderForRoute" style="z-index:;" >
        <nav class="navbar navbar-expand-lg d-lg-block d-xxl-block d-xl-block d-none">
          <div class="container">
            <a class="navbar-brand" href="{{route('home')}}"><h2>My Ztwen <em>School</em> <small class="text-lowercase text-white-50"><sup>school</sup></small> </h2></a>
            <button style="background-color: none !important" class="navbar-toggler border border-white bg-transparent" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
              <span class="d-none">{{ $target }}</span>
            </button>
            <div class="collapse navbar-collapse " id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <x-z-linker :routeName="'home'" :isActive="request()->routeIs('home')">
                        {{ __('Accueil') }}
                    </x-z-linker>
                    <x-z-linker :routeName="'visitation'" :isActive="request()->routeIs('visitation')">
                        {{ __('Visiter') }}
                    </x-z-linker>
                    @auth
                        <x-z-linker :params="['id' => $user->id]" :routeName="'parent_profil'" :isActive="request()->routeIs('parent_profil')">
                            {{ __('Espace Parents') }}
                        </x-z-linker>
                        @isTeacher()
                            <x-z-linker :routeName="'upload_epreuves'" :isActive="request()->routeIs('upload_epreuves')">
                                {{ __('Envoi Epreuves') }}
                            </x-z-linker>
                        @endif
                        @isAdmin($user)
                        <x-z-linker :routeName="'admin'" :isActive="request()->routeIs('admin')">
                            {{ __('Administration') }}
                        </x-z-linker>
                        @endisAdmin
                        <li class="nav-item cursor-pointer">
                                <x-z-linker :params="['id' => $user->id]" :routeName="'user_profil'" :isActive="request()->routeIs('user_profil')">
                                    <span class="fa fa-user text-success"></span> {{  mb_substr($user->pseudo, 0, 7) }}
                                </x-z-linker>
                            {{-- <div class=" sm:flex sm:items-center sm:ml-6">
                                @include('livewire.components.user.user-dropdown-component')
                            </div> --}}
                        </li>
                    @endauth
                    @guest
                        @routeHas('registration')
                            <li class="nav-item cursor-pointer">
                                <a href="{{route('registration')}}" class="nav-link registerModalOpen @isRoute('registration') active disabled @endisRoute " >S'inscrire
                                <span class="sr-only">(current)</span>
                                </a>
                            </li> 
                        @endrouteHas
                        @routeHas('connexion')
                            <li class="nav-item cursor-pointer">
                                <a href="{{route('connexion')}}" class="nav-link loginOpen @isRoute('connexion') active disabled @endisRoute " >Connexion
                                    <span class="sr-only">(current)</span>
                                </a>
                            </li> 
                        @endrouteHas
                    @endguest
                </ul>
            </div>
          </div>
        </nav>
      </header> 
      {{-- HEADER FOR SMALL SCREEN --}}
      <header class="d-block d-xl-none d-xxl-none">
        <main class="d-block d-xl-none d-xxl-none">
            <nav class="navbar navbar-dark">
                <div class="container-fluid">
                  <a class="navbar-brand" href="{{Route::has('home') ? route('home') : url('/')}}"><h2>ZtweN <em>School</em> <small class="text-lowercase text-muted"><sup class="text-white-50">market</sup></small> </h2></a>
                  <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbarDark" aria-controls="offcanvasNavbarDark">
                    <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="offcanvas offcanvas-start text-white bg-dark" tabindex="-1" id="offcanvasNavbarDark" aria-labelledby="offcanvasNavbarDarkLabel">
                    <div class="offcanvas-header">
                      <h5 class="offcanvas-title" id="offcanvasNavbarDarkLabel">
                        <span class="bi-house mr-2"></span> Menu
                      </h5>
                      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                      <ul class="navbar-nav justify-content-end text-white flex-grow-1 pe-3">
                        <x-z-linker :routeName="'home'" :isActive="request()->routeIs('home')">
                            <span class="bi-house mr-2"></span>
                            <span>{{ __('Accueil') }}</span>
                        </x-z-linker>
                        <x-z-linker :routeName="'products'" :isActive="request()->routeIs('products')">
                            <span class="bi-tools mr-2"></span>
                            <span>{{ __('Visiter') }}</span>
                        </x-z-linker>
                        <x-z-linker :routeName="'categories'" :isActive="request()->routeIs('categories')">
                            <span class="bi-bookmarks mr-2"></span>
                            <span>{{ __('Parent') }}</span>
                            <span class="bi-chevron-down float-end"></span>
                        </x-z-linker>
                        @isAdmin()
                        <x-z-linker :routeName="'admin'" :isActive="request()->routeIs('admin')">
                            <span class="fa fa-user-secret mr-2"></span>
                            <span>{{ __('Administration') }}</span>
                        </x-z-linker>
                        @endisAdmin
                        @auth
                        <x-z-linker :params="['id' => $user->id]" :routeName="'user-profil'" :isActive="request()->routeIs('user-profil')">
                            <span class="bi-person mr-2"></span>
                            <span>{{ __('Profil') }}</span>
                        </x-z-linker>
                        @endauth
                        @guest
                        <x-z-linker :routeName="'login'" :isActive="request()->routeIs('login')">
                            <span class="bi-person-check mr-2"></span>
                            <span>{{ __('Se connecter') }}</span>
                        </x-z-linker>
                        <x-z-linker :routeName="'registration'" :isActive="request()->routeIs('registration')">
                            <span class="bi-person-plus mr-2"></span>
                            <span>{{ __("S'inscrire") }}</span>
                        </x-z-linker>
                        @endguest
                        </ul>
                        <form class="d-flex mt-3" role="search">
                            <input class="form-control me-2 bg-transparent border border-white" type="search" placeholder="Lancer une recherche..." aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Rechercher</button>
                        </form>
                        <div class="position-absolute bottom-0 py-2">
                            @auth
                                <div>
                                    @if($user && $user->current_photo)
                                        <img width="80" height="300" class="rounded-circle border border-white" src="/storage/profilPhotos/{{$user->currentPhoto()}}" alt="mon profil">
                                    @elseif($user && !$user->current_photo)
                                        <img width="80" height="300" class="rounded-circle border border-white" src="{{$user->currentPhoto()}}" alt="mon profil">
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>
                  </div>
                </div>
              </nav>
          </main>
      </header>
</div>