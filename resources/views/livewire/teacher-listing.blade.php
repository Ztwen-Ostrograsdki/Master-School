<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse"></h5>
                <div class="card-tools">
                    
                </div>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto mr-3">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir la liste</a>
                            <a title="Importer les enseignants de l'an dernier: les enseignants de l'année-scolaire  {{ $school_year_befor_model ? $school_year_befor_model->school_year : ' dernière' }}" wire:click="importLastYearTeachersToThisYear" class="dropdown-item" tabindex="-1" href="#">Importer les anciens de l'an dernier</a>
                            
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
                <div class="card-header d-flex justify-content-between p-0">
                    <span class="ml-3 my-2">
                        @if(count($teachers) > 0)
                            <span title="Ajouter un enseignant" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="addNewTeacher">
                                <span class="bi-person-plus"></span>
                                <span>Ajouter</span>
                            </span>
                        @else
                            <span title="Importer les enseignants de l'année-scolaire {{ $school_year_befor_model ? $school_year_befor_model->school_year : ' dernière' }}" class="float-right text-white-50 border z-scale p-2 px-5 rounded cursor-pointer bg-orange" wire:click="importLastYearTeachersToThisYear">
                                    <span class="bi-person-plus"></span>
                                    <span>Importer des enseignants</span>
                            </span>
                        @endif
                    </span>
                    @if(count($teachers) > 0)
                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item">
                                <select wire:change="changeSection('level')" wire:model="level_id_selected" class="form-select z-bg-secondary custom-select">
                                    <option value=""> Enseignants selon le cycle </option>
                                    @foreach($levels as $lev)
                                        <option value="{{$lev->id}}"> {{ $lev->getName() }} </option>
                                    @endforeach
                                </select>
                            </li>

                            <li class="nav-item mx-2">
                                <select wire:change="changeSection('classe')" wire:model="classe_id_selected" class="form-select z-bg-secondary custom-select">
                                    <option value=""> Enseignants selon la classe </option>
                                    @foreach($classes as $cl)
                                        <option value="{{$cl->id}}"> {{ $cl->name }} </option>
                                    @endforeach
                                </select>
                            </li>

                            <li class="nav-item">
                                <select wire:change="changeSection('subject')" wire:model="subject_id_selected" class="form-select z-bg-secondary custom-select">
                                    <option value=""> Enseignants selon la matière </option>
                                    @foreach($subjects as $sub)
                                        <option value="{{$sub->id}}"> {{ $sub->name }} </option>
                                    @endforeach
                                </select>
                            </li>
                        </ul>
                    @else
                        <span class="ml-auto text-orange pt-3 mr-3 font-italic">Aucun enseignant inscrit encore au titre de l'année-scolaire {{ session('school_year_selected') }}</span>
                    @endif
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        @if($is_loading)
                            <div class="w-100 d-flex justify-content-center flex-column">
                                @livewire('solar-system-loader')  
                            </div>
                        @else
                            <div class="tab-pane active">
                                @livewire('teacher-table-list', ['classe_id' => $classe_id_selected, 'level_id' => $level_id_selected, 'subject_id' => $subject_id_selected, 'baseRoute' => $baseRoute])
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
