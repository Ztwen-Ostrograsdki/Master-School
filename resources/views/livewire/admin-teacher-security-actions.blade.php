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
                                            <input placeholder="Veuillez entrer le nom ou le prénom de l'enseignant à retrouver ..." class="form-control bg-transparent py-1" type="text" name="search" wire:model="search">
                                        </form>
                                        <div wire:click="resetSearch" data-card-widget="collapse" class="btn-secondary rounded text-center p-1 cursor-pointer border border-white col-2">
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
        @if(!$start)
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
                            @livewire('teacher-table-list', ['classe_id' => $classe_id_selected, 'level_id' => $level_id_selected, 'subject_id' => $subject_id_selected])
                        </div>
                    </div>
                </div>
            </div>
            @if(count($teachers_selecteds) > 0)
            <div class="w-100 d-flex justify-content-center">
                <span wire:click="startProcess" class="btn btn-primary border py-2 w-50 d-flex flex-column">
                    <h6>Démarrer la procédure</h6>
                    <span class="text-dark"> {{ count($teachers_selecteds) > 0 ? count($teachers_selecteds) . ' enseignant(s) sélectionné(s) ' : ''}} </span>
                </span>
            </div>
            @else
                <h6 class="text-center text-warning">Veuillez sélectionner les enseignants!</h6>
            @endif
        </div>
        @endif
    </div>
    {{-- @else --}}
    @if(count($teachers_selecteds) > 0)
    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between p-0">
                    <span class="ml-1 my-2">
                        <span title="Fermer la fenêtre et afficher la page de sélections" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-orange" wire:click="hide">
                            <span class="bi-eye-slash"></span>
                            <span>Fermer la fenêtre</span>
                        </span>

                        <span title="Annuler la procédure" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-danger mx-2" wire:click="cancel">
                            <span class="bi-reply-all"></span>
                            <span>Annuler la Procédure</span>
                        </span>
                    </span>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item">
                            
                        </li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body p-2">
                    <div class="tab-content">
                        <div class="">
                            @foreach($teachers_table as $t)
                                <div class="card border rounded">
                                    <h6 class="d-flex justify-content-between p-2 m-0">
                                        <span>
                                            <span class="text-white-50">
                                                <span class="bi-person mr-2 text-orange"></span>
                                                Enseignant: 
                                            </span>
                                            <span class="text-white">{{ $t->name . ' ' . $t->surname }}</span>
                                        </span>
                                        <span>
                                            <span class="text-white-50">Spécialité: </span>
                                            <span class="text-white">{{ $t->speciality()->name }}</span>
                                        </span>
                                        <span>
                                            <span class="text-white-50">Nombre de classes: </span>
                                            <span class="text-white">{{ count($t->getTeachersCurrentClasses())}}</span>
                                        </span>
                                    </h6>
                                    <hr class="bg-white w-100 p-0 m-0">
                                    @if($t->hasClasses())
                                    <div class="row p-2 m-2 justify-content-between">
                                        @foreach($t->getTeachersCurrentClasses() as $cl)
                                            <div class="card border border-secondary rounded shadow p-0 m-1 zw-45">
                                                <h6 class="d-flex justify-content-between p-2 m-0">
                                                    <span>
                                                        <span class="bi-house mr-2"></span>
                                                        <span class="text-white-50">Classe: </span>
                                                        <span class="text-warning">{{ $cl->name }}</span>
                                                    </span>
                                                    <span>
                                                        <span class="text-white-50">Effectif: </span>
                                                        <span class="text-white">0{{ count($cl->getPupils(session('school_year_selected'))) }}</span>
                                                    </span>
                                                    <span>
                                                        <span class="text-white-50">Prof Principal </span>
                                                        <span class="text-white">{{ 'Non défini' }}</span>
                                                    </span>
                                                </h6>
                                                <hr class="bg-orange w-100 p-0 m-0">

                                                <div class="d-flex p-2">
                                                    @if($t->teacherCanUpdateMarksInThisClasse($cl->id))
                                                        <span wire:click="submit({{$t->id}},{{$cl->id}}, 'locked_marks_updating')" class="btn btn-secondary m-1">Ver. Edition notes</span>
                                                    @else
                                                        <span wire:click="submit({{$t->id}},{{$cl->id}}, 'locked_marks_updating')" class="btn btn-primary m-1">Autoriser Edition notes</span>
                                                    @endif
                                                    @if(!$t->teacherCanAccess($cl->id, 'closed'))
                                                        <span class="btn btn-primary m-1">Ouvrir accès</span>
                                                    @else
                                                        <span wire:click="submit({{$t->id}},{{$cl->id}}, 'closed_classe')" class="btn btn-primary m-1">Bloquer Accès</span>
                                                    @endif
                                                    @if(!$t->teacherCanAccess($cl->id, 'locked'))
                                                        <span class="btn btn-success m-1">Déver. classe</span>
                                                    @else
                                                        <span wire:click="submit({{$t->id}},{{$cl->id}}, 'locked_classe')" class="btn btn-success m-1">Ver. classe</span>
                                                    @endif
                                                    @if(!$t->teacherCanAccessMarks($cl->id))
                                                        <span class="btn btn-warning m-1">Déver. notes</span>
                                                    @else
                                                        <span wire:click="submit({{$t->id}},{{$cl->id}}, 'locked_marks')" class="btn btn-warning m-1">Vér. notes</span>
                                                    @endif
                                                    <span class="btn btn-secondary m-1">Vider notes</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="d-flex p-2 z-bg-secondary justify-content-between">
                                        <h6 class="small">Effectuer une action globale sur les classe de Mr/Mme <span class="text-orange">{{ $t->name . ' ' . $t->surname }}</span> </h6>
                                        <div class="d-flex justify-content-end">
                                            <span wire:click="submit({{$t->id, null}}, 'locked_marks_updating')" class="btn btn-secondary m-1">Ver. Edition notes</span>
                                            <span wire:click="submit({{$t->id, null}}, 'locked_marks_updating')" class="btn btn-primary m-1">Autoriser Edition notes</span>
                                            <span class="btn border-orange btn-primary m-1">Ouvrir accès</span>
                                            <span wire:click="submit({{$t->id,null}}, 'closed_classe')" class="btn border-orange btn-primary m-1">Bloquer Accès</span>
                                            <span class="btn border-orange btn-success m-1">Déver. classe</span>
                                            <span wire:click="submit({{$t->id,null}}, 'locked_classe')" class="btn border-orange btn-success m-1">Ver. classe</span>
                                            <span class="btn border-orange btn-warning m-1">Déver. notes</span>
                                            <span wire:click="submit({{$t->id,null}}, 'locked_marks')" class="btn border-orange btn-warning m-1">Vér. notes</span>
                                            <span class="btn border-orange btn-secondary m-1">Vider notes</span>
                                        </div>
                                    </div>
                                    @else
                                        <div class="mx-auto w-75 p-2 text-center">
                                            <h6 class="text-center w-100"> Mr/Mme <span class="text-orange">{{ $t->name . ' ' . $t->surname }}</span> n'a aucune classes pour le moment. Cliquer sur le bouton en bas pour attribuer des classes </h6>
                                            <span wire:click="manageTeacherClasses({{$t->id}})" class="btn w-50 mx-auto py-2 border btn-warning">Attribuer classes maintenant</span>
                                        </div> 
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @if(count($teachers_selecteds) > 1)
                    <div class="d-flex p-2 z-bg-secondary border rounded border-orange justify-content-between">
                        <h6 class="small">Effectuer une action globale sur l'ensemble des enseignants sélectionés</h6>
                        <div class="d-flex justify-content-end">
                            <span class="btn btn-secondary m-1">Ver. Edition notes</span>
                            <span class="btn btn-primary m-1">Autoriser Edition notes</span>
                            <span class="btn border-dark btn-primary m-1">Fermer classes</span>
                            <span class="btn border-dark btn-success m-1">Ver. classes</span>
                            <span class="btn border-dark btn-warning m-1">Ver. notes</span>
                            <span class="btn border-dark btn-secondary m-1">Vider notes</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if($start)
            <div class="w-100 d-flex justify-content-center">
                <span wire:click="startProcess" class="btn btn-success border py-2 w-50 d-flex flex-column">
                    <h6>Valider la procédure</h6>
                    <span class="text-dark"> {{ count($teachers_selecteds) > 0 ? count($teachers_selecteds) . ' enseignant(s) sélectionné(s) ' : ''}} </span>
                </span>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>
