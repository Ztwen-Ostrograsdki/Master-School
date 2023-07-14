<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') !== 'classe_general_stats' && session('classe_profil_section_selected') !== 'time_plan')
            <div class="card-header bg-dark my-2"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Informations Générales {{ $classe ? 'de la ' . $classe->name : "" }}</h5>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                  <div class="card-tools">
                    @if($classe)
                    <ul class="nav nav-pills ml-auto mr-3">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" wire:click="deleteAllPupil({{$classe->id}})" tabindex="-1" href="#">Rafraichir la classe</a>
                            <a class="dropdown-item" tabindex="-1" wire:click="refreshAllMarks" href="#">Vider toutes les notes de cette classe</a>
                            <a class="dropdown-item @if(session()->has('classe_subject_selected') && session('classe_subject_selected')) d-none @endif " tabindex="-1" wire:click="resetMarks" href="#">Rafraichir les notes de cette matière</a>
                            <a class="dropdown-item" wire:click="resetAbsences" tabindex="-1" href="#">Rafraichir les absences</a>
                            <a class="dropdown-item" wire:click="resetLates" tabindex="-1" href="#">Rafraichir les retards</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Créer une nouvelle classe</a>
                            <a class="dropdown-item" wire:click="createNewClasse" tabindex="-1" href="#">Créer une classe</a>
                            <a class="dropdown-item" wire:click="editClasseGroup({{$classe->id}})" tabindex="-1" href="#">Modifier la promotion</a>
                            <a wire:click="editClasseSubjects({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Définir les matières</a>
                            <a wire:click="settingsOnMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Effectuer une opération sur les notes</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="fa fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Effectif 
                                        (<b class="text-warning">
                                            {{ $classe ? count($classe->getPupils(session('school_year_selected'))) : 'vide'}}
                                        </b>)
                                    </span>
                                    <span class="info-box-number d-flex flex-column m-0 p-0">
                                        <span class="small">
                                            <i class="font-italic"> Garçons </i> : 
                                            <small> 
                                                {{ $classe ? count($classe->getClassePupilsOnGender('male', session('school_year_selected'))) : '00'}}
                                            </small>
                                        </span>

                                        <span class="small">
                                            <i class="font-italic"> Filles </i> : 
                                            <small> 
                                                {{ $classe ? count($classe->getClassePupilsOnGender('female', session('school_year_selected'))) : '00' }}
                                            </small>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-primary">
                                <span class="info-box-icon"><i class="fa fa-user-nurse"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les Notes
                                        (<b class="text-warning">
                                            {{ $classe ? $classe->marks()->where('marks.school_year_id', $school_year_model->id)->count() : '00'}}
                                        </b>)
                                    </span>
                                    <span class="info-box-number d-flex flex-column m-0 p-0">
                                        <span class="small">
                                            <i class="font-italic"> {{ $semestre_type }} 1 </i> : 
                                            <small> 
                                                {{ $classe ? $classe->marks()->where('marks.school_year_id', $school_year_model->id)->where($semestre_type, 1)->count() : '00'}}
                                            </small>
                                        </span>

                                        <span class="small">
                                            <i class="font-italic"> {{ $semestre_type }} 2 </i> : 
                                            <small> 
                                                {{ $classe ? $classe->marks()->where('marks.school_year_id', $school_year_model->id)->where($semestre_type, 2)->count() : '00'}}
                                            </small>
                                        </span>

                                        @if($semestre_type == 'Trimestre')
                                            <span class="small">
                                                <i class="font-italic"> {{ $semestre_type }} 3 </i> : 
                                                <small> 
                                                    {{ $classe ? $classe->marks()->where('marks.school_year_id', $school_year_model->id)->where($semestre_type, 3)->count() : '00'}}
                                                </small>
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-success">
                                <span class="info-box-icon"><i class="far fa-heart"></i></span>
                                <div class="info-box-content">
                                  <span class="info-box-text">Scolarités</span>
                                  <span class="info-box-number">25%</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-danger">
                                <span class="info-box-icon"><i class="fa fa-cloud-download-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Emploi du temps</span>
                                    <span class="info-box-number">45%</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="far fa-comment"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les enseignants</span>
                                    <span class="info-box-number"> {{ $classe ? count($classe->getClasseCurrentTeachers()) : '00' }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($classe)
                        <div>
                            <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark my-2">
                                <div class="card-header bg-dark"> 
                                    <h5 class="card-title cursor-pointer text-white-50" data-card-widget="collapse">Listes des enseignants de la classe </h5>
                                  <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                                      <i class="fa fa-times"></i>
                                    </button>
                                  </div>
                                    <div class="card-tools">
                                        
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="container-fluid m-0 p-0 w-100">
                                        <div class="card-deck w-100 p-0 m-0">
                                            <div class="card active" href="#tab_1" data-toggle="tab">
                                                @php
                                                    $teachers = $classe->getClasseCurrentTeachers();
                                                @endphp

                                                @foreach($teachers as $tt)

                                                    <div class="d-flex justify-content-start p-1">
                                                        <h6 class="text-white-50 pt-2 pb-1">
                                                            <span class="bi-person"></span>
                                                            <span class="text-warning">Prof:</span> {{  $tt->getFormatedName() }}
                                                            (<span class="text-orange">{{  $tt->speciality()->name }}</span>)
                                                        </h6>
                                                        <h6 class="text-white-50 pt-2 pb-1 mx-4">
                                                            <span class="bi-person-badge"></span>
                                                            <span class="text-warning">Compte:</span> 
                                                            <a class="text-white-50" href="{{route('user_profil', $tt->user->id)}}">
                                                                {{  $tt->user->pseudo . '  (' .  $tt->user->email . ')' }}
                                                            </a>
                                                        </h6>
                                                        <span class="d-flex ml-3 justify-content-between flex-column">
                                                            @foreach($tt->teacherHasCourseAtThisTime($classe->id) as $tp)
                                                                <small class="text-white-50 font-italic pt-1">
                                                                    {{ $tp }}
                                                                </small>
                                                            @endforeach
                                                        </span>
                                                    </div>
                                                    <hr class="bg-secondary text-secondary m-0 p-0 w-100">

                                                @endforeach

                                                
                                            </div>
                                            
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark my-2">
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
                    
                </div>
            </div>
        </div>
        @endif
    </div>


    <div class="row w-100 mx-auto mt-1 p-2">
        <div class="col-12">
          <!-- Custom Tabs -->
          <div class="card">
            <div class="card-header d-flex p-0">
              <h3 class="card-title p-3">
                @if (!$editingClasseName)
                  {{ session('classe_selected') }}
                @else
                <form wire:submit.prevent="updateClasseName" autocomplete="off" class="my-1 d-flex p-2 cursor-pointer w-100 shadow border border-secondary">
                  <div class="d-flex justify-between zw-80">
                      <div class="w-100">
                        <x-z-input :type="'text'" :error="$errors->first('classeName')" :modelName="'classeName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                      </div>
                  </div>
                  <div class="d-inline-block float-right text-right zw-20">
                      <span wire:click="cancelEditingName" title="Fermer la fenêtre d'édition" class="fa cursor-pointer text-danger p-2">X</span>
                  </div>
                </form>  
                @endif
                @if ($classe && !$editingClasseName)
                  <span wire:click="editClasseName({{$classe->id}})" class="fa fa-edit cursor-pointer mx-2"></span>
                @endif
                @if (!$editingClasseName)
                <form class="d-inline" action="">
                    @csrf()
                    <select id="semestre_selected" wire:model="semestre_selected" class="form-select ml-3">
                      <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                        @foreach ($semestres as $semestre)
                            <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                        @endforeach
                    </select>
                </form>
                @endif
              </h3>
              @if($classe || $classeSelf)
              <ul class="nav nav-pills ml-auto p-2">
                <li wire:click="setClasseProfilActiveSection('liste')" class="nav-item"><a class="nav-link @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') == 'liste') active @elseif(!session()->has('classe_profil_section_selected')) active @endif border border-white" href="#tab_1" data-toggle="tab">Liste</a>
                </li>
                <li wire:click="setClasseProfilActiveSection('time_plan')" class="nav-item"><a class="nav-link @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'time_plan') active @endif border border-white mx-1" href="#tab_6" data-toggle="tab">Emp. Temps</a>
                </li>
                <li wire:click="setClasseProfilActiveSection('marks')" class="nav-item"><a class="nav-link @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'marks') active @endif border border-white mx-1" href="#tab_2" data-toggle="tab">Les Notes</a>
                </li>
                <li wire:click="setClasseProfilActiveSection('related_marks')" class="nav-item"><a class="nav-link @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'related_marks') active @endif border border-white" href="#tab_3" data-toggle="tab">Bonus - Sanctions</a>
                </li>
                <li wire:click="setClasseProfilActiveSection('lates_absences')" class="nav-item"><a class="nav-link @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'lates_absences') active @endif border border-white mx-1" href="#tab_4" data-toggle="tab">Absence</a>
                </li>
                <li wire:click="setClasseProfilActiveSection('classe_general_stats')" class="nav-item"><a class="nav-link @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'classe_general_stats') active @endif border border-white mx-1" href="#tab_5" data-toggle="tab">
                    Stats Gles
                    <span class="bi-graph-up"></span>
                </a>
                </li>
              </ul>
              @else
              <h3 class="card-title ml-auto p-3 float-right text-warning">
                <span class="bi-lock mx-2"></span>
                <span>La classe de <b class="text-orange">{{ session('classe_selected') }} </b> est vide pour l'année scolaire <b class="text-orange">{{ session('school_year_selected') }} </b> </span>
              </h3>
              @endif
            </div><!-- /.card-header -->
            <div class="card-body">
              <div class="tab-content">
                @if (!$classe)
                <div class="w-100 border border-primary p-2 m-2">
                  <blockquote class="text-info">
                     <h5>
                      <span class="text-white-50">
                        Veuillez cliquer sur le bouton pour lier cette classe à l'année {{ session('school_year_selected') }}
                      </span>
                      <span wire:click="joinClasseToSchoolYear({{$classeSelf->id}})" class="text-center rounded btn-primary cursor-pointer border p-2 m-2 d-block">
                        Générer {{ session('classe_selected') }} pour l'année scolaire {{ session('school_year_selected') }}
                      </span>
                     </h5>
                  </blockquote>
                </div>
                @endif
                @if($classe)
                {{-- {{ dd($classe->classePupils) }} --}}
                <div class="tab-pane la-liste-de-la-classe @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') == 'liste') active @elseif(!session()->has('classe_profil_section_selected')) active @endif" id="tab_1">
                    @livewire('classe-pupils-lister', ['classe_id' => $classe->id])
                </div>
                <div class="tab-pane les-notes-de-la-classe @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'marks') active @endif" id="tab_2">
                    @livewire('classe-marks-lister', ['classe_id' => $classe->id])
                </div>
                <div class="tab-pane les-retard-de-la-classe @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'related_marks') active @endif" id="tab_3">
                    @livewire('classe-pupil-related-mark', ['classe_id' => $classe->id])
                </div>
                <div class="tab-pane les-absences-de-la-classe @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'lates_absences') active @endif" id="tab_4">
                    @livewire('classe-presence-absence', ['classe_id' => $classe->id])
                </div>
                <div class="tab-pane les-notes-générales-de-la-classe @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'classe_general_stats') active @endif" id="tab_5">
                    @livewire('classe-generals-stats', ['classe_id' => $classe->id])
                </div>
                <div class="tab-pane emploi-du-temps-de-la-classe @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') == 'time_plan') active @endif" id="tab_6">
                    <div class="mx-auto w-100 my-2">
                        <blockquote class="text-warning">
                            <div class="d-flex justify-content-between w-100">
                                <h6 class="m-0 p-0 h6 text-white-50 mt-2">
                                    EMPLOI DU TEMPS DE LA CLASSE DE
                                    <span class="text-orange mx-1">
                                        @if($classe)
                                            @php
                                                $cl = $classe->getNumericName();
                                            @endphp
                                            <span class="">
                                                {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                            </span>
                                        @else
                                            <span>Classe inconnue</span>
                                        @endif
                                    </span>  
                                    DE L'ANNEE SCOLAIRE
                                    <span class="text-warning"> {{ session('school_year_selected') ? session('school_year_selected') : 'En cours...' }}</span>
                                    <span class="float-right text-muted"> </span>
                                </h6>
                                @if(auth()->user()->isAdminAs('master'))
                                    <span class="">
                                        <span title="Insérer un nouvel emploi de temps de cette classe" class="float-right text-white-50 border p-2 px-5 rounded cursor-pointer bg-primary" wire:click="addTimePlan">
                                            <span class="bi-download"></span>
                                            <span>Ajouter</span>
                                        </span>

                                        <span title="Supprimer les emplois de temps de la classe de {{$classe->name}} de cette année scolaire" class="float-right mx-1 text-white-50 border p-2 px-5 rounded cursor-pointer bg-orange" wire:click="deleteClasseTimePlans">
                                            <span class="fa fa-recycle"></span>
                                            <span>Tout rafraichir</span>
                                        </span>
                                    </span>
                                @endif
                            </div>
                        </blockquote>
                    </div>
                    @livewire('time-plan-lister', ['classesToShow' => $classesToShow, 'subject_id' => null, 'intoClasseProfil' => true])
                </div>
                @endif
              </div>
            </div><!-- /.card-body -->
          </div>
          <!-- ./card -->
        </div>
        <!-- /.col -->
      </div>

</div>
