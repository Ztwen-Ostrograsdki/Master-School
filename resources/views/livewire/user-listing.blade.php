<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Effectuer une recherche...</h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
                    <div class="card-tools">
                    <ul class="nav nav-pills ml-auto mr-3">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir la liste</a>
                            
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card active" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-transparent">
                                <span class="info-box-icon"><i class="fa bi-search"></i></span>
                                <div class="info-box-content">
                                    <div class="d-flex justify-content-between">
                                        <form action="" class="col-10">
                                            <input placeholder="Veuillez entrer le nom ou le prénom de l'apprenant à retrouver ..." class="form-control bg-transparent py-1" type="text" name="search" wire:model="search">
                                        </form>
                                        <div x-on:click="@this.call('resetSearch')" data-card-widget="collapse" class="btn-secondary rounded text-center p-1 cursor-pointer border border-white col-2">
                                            <span>Annuler</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex p-0">
                    <ul class="nav nav-pills ml-auto p-2">
                        <li wire:click="setUsersActiveSection('all')" class="nav-item"><a class="nav-link @if(session()->has('users_section_selected') && session('users_section_selected') == 'all') active @elseif(!session()->has('users_section_selected')) active @endif border border-white" href="#tab_1" data-toggle="tab">Tout</a></li>
                        <li wire:click="setUsersActiveSection('confirmed')" class="nav-item"><a class="nav-link @if(session()->has('users_section_selected') && session('users_section_selected') && session('users_section_selected') == 'confirmed') active @endif border border-white mx-1" href="#tab_2" data-toggle="tab">Les Confirmés</a>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        @if(true)
                        <div class="tab-pane @if(session()->has('users_section_selected') && session('users_section_selected') == 'all') active @elseif(!session()->has('users_section_selected')) active @endif" id="tab_1">
                            @livewire('user-listing-table', ['target' => $active_section])
                        </div>
                        <div class="tab-pane @if(session()->has('users_section_selected') && session('users_section_selected') && session('users_section_selected') == 'confirmed') active @endif" id="tab_2">
                            @livewire('user-listing-table', ['target' => $active_section])
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
