<div wire:ignore.self>
    <nav class="main-header navbar navbar-expand navbar-dark">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="{{route('home')}}" class="nav-link">Acceuil</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="#" class="nav-link">Contact</a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a title="L'année scolaire que vous avez sélectionnée" href="#" class="nav-link text-warning">{{ $school_year }}</a>
            </li>
        </ul>
    
        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
          <!-- Navbar Search -->
          <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
                <i class="fas fa-search form-search-opener"></i>
            </a>
            <div  class="navbar-search-block form-searcher">
                <form class="form-inline">
                    <div class="input-group input-group-sm mx-auto col-6">
                        <input wire:model="search" class="form-control form-control-navbar"  placeholder="Taper un mot clé à rechercher sur le site...">
                        <div class="input-group-append">
                            <span class="btn btn-navbar" >
                                <i class="fas fa-search"></i>
                            </span>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                                <i class="fas fa-times form-search-closer"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
          </li>
    
          <!-- Messages Dropdown Menu -->
            @livewire('admin-messagers-center-component')
        <!-- Notifications Dropdown Menu -->
            @livewire('admin-notifications-center-component')

            @livewire('online-users-lister')
          
          
          <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
                <i class="fas fa-th-large"></i>
            </a>
          </li>
        </ul>
      </nav>
</div>
