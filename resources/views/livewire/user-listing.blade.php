<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">

                </h5>
              <div class="card-tools">
               
              </div>
                    <div class="card-title">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" tabindex="-1" href="#">Supprimer toutes les clés admins actives</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Bloqués tous les administrateurs</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Supprimer tous les administrateurs</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Verrouiller tous les comptes</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Déconnecter tous les comptes</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Déconnecter tous les administrateurs</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Déconnecter tous les enseignants</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Bloquer tous les enseignants</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Déconnecter tous les parents</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Bloquer tous les parents</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir la liste</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir la liste</a>
                            
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            
        </div>
    </div>

    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <ul class="nav nav-pills ml-auto p-2">
                        <span class="nav-item">
                            <select  wire:model="active_section" class="form-select z-bg-secondary custom-select">
                                @foreach($sections as $section => $title_text)
                                    <option value="{{$section}}"> {{ $title_text }} </option>
                                @endforeach
                            </select>
                        </span>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="">
                            @livewire('user-listing-table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
