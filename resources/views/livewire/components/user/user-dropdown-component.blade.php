<div>
    @auth
    <x-dropdown align="right" width="48" class="text-bold text-dark">
        <x-slot name="trigger">
            <x-responsive-nav-link class="text-white-50 cursor-pointer z-color-hover-orange">
                <span class="fa fa-user text-success pt-3 pb-2"></span> {{  mb_substr(Auth::user()->pseudo, 0, 7) }}
                {{-- @livewire('notifications-center') --}}
            </x-responsive-nav-link>
        </x-slot>
        <x-slot name="content" :class="'text-left'">
            <!-- Authentication -->
            @routeHas('user_profil')
                @isNotRoute('user_profil')
                <x-dropdown-link class="nav-item text-left w-100 p-0 m-0 z-hover-secondary text-bold" href="{{route('user_profil', Auth::user()->id)}}">
                    <span class="fa mr-3 d-flex">
                        <img width="30" class="border rounded-circle" src="{{Auth::user()->__profil('100')}}" alt="mon profil">
                        <span class="mt-1 mx-2">
                            {{ __('Profil') }}
                        </span>
                    </span>
                    
                </x-dropdown-link>
                @endisNotRoute
            @endrouteHas
            <x-dropdown-link class="nav-item text-left w-100 p-0 m-0 z-hover-secondary text-bold"  href="#">
                <span wire:click="openModalForMyNotifications">
                    <span class="fa bi-envelope-open mr-3"></span> 
                    <span>
                        {{-- @livewire('my-notifications-counter') --}}
                    </span>
                </span>
            </x-dropdown-link>
                <span class="fa fa-wechat mr-3"></span>{{ __('Messenger') }}
            @isAdmin()
                <x-dropdown-link class="nav-item text-left w-100 p-0 m-0 z-hover-secondary text-bold" data-toggle="modal" data-target="#createProductModal" href="#" wire:click="createNewProduct">
                    <span class="fa fa-cart-plus mr-3"></span>{{ __('Ajouter un article') }}
                </x-dropdown-link>
                <x-dropdown-link class="nav-item text-left w-100 p-0 m-0 z-hover-secondary text-bold" data-toggle="modal" data-target="#createCategoryModal" href="#">
                    <span class="fa fa-plus mr-3"></span>{{ __('Une catégorie') }}
                </x-dropdown-link>
            @endisAdmin
            <x-dropdown-link class="nav-item text-left w-100 p-0 m-0 z-hover-secondary text-bold" data-toggle="modal" data-dismiss="modal" data-target="#logoutModal" href="#">
                <span class="fa fa-upload  mr-3"></span>{{ __('Déconnexion') }}
            </x-dropdown-link>
        </x-slot>
    </x-dropdown>
    @endauth
</div>