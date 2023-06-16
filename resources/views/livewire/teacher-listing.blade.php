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
                <div class="card-header d-flex justify-content-between p-0">
                    <span class="ml-3 mt-2">
                        <span title="Ajouter un enseignant" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="addNewTeacher">
                            <span class="bi-person-plus"></span>
                            <span>Ajouter</span>
                        </span>
                    </span>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item">
                            <select wire:change="changeSection('level')" wire:model="level_id_selected" class="form-select z-bg-secondary custom-select">
                                <option value=""> Liste enseignants selon le cycle </option>
                                @foreach($levels as $lev)
                                    <option value="{{$lev->id}}"> {{ $lev->getName() }} </option>
                                @endforeach
                            </select>
                        </li>

                        <li class="nav-item mx-2">
                            <select wire:change="changeSection('classe')" wire:model="classe_id_selected" class="form-select z-bg-secondary custom-select">
                                <option value=""> Liste enseignants selon la classe </option>
                                @foreach($classes as $cl)
                                    <option value="{{$cl->id}}"> {{ $cl->name }} </option>
                                @endforeach
                            </select>
                        </li>

                        <li class="nav-item">
                            <select wire:change="changeSection('subject')" wire:model="subject_id_selected" class="form-select z-bg-secondary custom-select">
                                <option value=""> Liste enseignants selon la matière </option>
                                @foreach($subjects as $sub)
                                    <option value="{{$sub->id}}"> {{ $sub->name }} </option>
                                @endforeach
                            </select>
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane active">
                            @livewire('teacher-table-list', ['classe_id' => $classe_id_selected, 'level_id' => $level_id_selected, 'subject_id' => $subject_id_selected, 'baseRoute' => $baseRoute])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
