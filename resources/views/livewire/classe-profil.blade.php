<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            @if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected') && session('classe_profil_section_selected') !== 'classe_general_stats' && session('classe_profil_section_selected') !== 'time_plan')
            <div class="card-header bg-dark my-2"> 
                {{-- <h5 class="card-title cursor-pointer mr-auto " data-card-widget="collapse">Informations Générales {{ $classe ? 'de la ' . $classe->name : "" }}</h5> --}}
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                  </div>
                  <div class="card-title">
                    @if($classe)
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item dropdown">
                          <a class="nav-link text-white dropdown-toggle border border-warning" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" title="Déplacer un apprenant de cette classe vers une autre classe" wire:click="movePupilFromThisClasse({{$classe->id}})" tabindex="-1" href="#">Déplacer un apprenant de cette classe</a>
                            <a class="dropdown-item" title="Importer des apprenants" wire:click="addNewsPupils({{$classe->id}})" tabindex="-1" href="#">Ajouter des apprenants à cette classe</a>
                            <a class="dropdown-item" title="Importer des apprenants dans cette classe" wire:click="importPupilsIntoClasse({{$classe->id}})" tabindex="-1" href="#">Importer des apprenants dans cette classe</a>
                            @if($not_stopped)
                                <a class="dropdown-item" wire:click="optimizeClasseAveragesIntoDatabase({{$classe->id}})" tabindex="-1" href="#">Optimiser les moyennes en base de données</a>
                                <a class="dropdown-item" wire:click="optimizeSemestrialAverageFromDatabase({{$classe->id}})" tabindex="-1" href="#">Charger les moyennes semestrielles</a>
                                <a class="dropdown-item" tabindex="-1" wire:click="refreshClasseMarks('{{$classe->id}}')" href="#">Vider les notes de cette classe</a>
                                <a class="dropdown-item" wire:click="resetAbsences" tabindex="-1" href="#">Rafraichir les absences</a>
                                <a class="dropdown-item" wire:click="resetLates" tabindex="-1" href="#">Rafraichir les retards</a>
                                <a class="dropdown-item" wire:click="updateClassePupilsPersoDataFromFile"  tabindex="-1" href="#">Mettre à jour les données à partir d'un fichier</a>
                            @endif
                            <a class="dropdown-item" wire:click="updateClassePupilsNames"  tabindex="-1" href="#">Mettre à jour les noms et prenoms</a>
                            <a class="dropdown-item" wire:click="updatePupilsLTPKMatricule"  tabindex="-1" href="#">Mettre à jour les matricules</a>
                            @if($not_stopped)
                                <a class="dropdown-item" wire:click="insertClasseMarks"  tabindex="-1" href="#">Insérer des notes de classe</a>
                                <a class="dropdown-item" wire:click="convertClasseLastMarksToParticipateMarks"  tabindex="-1" href="#">Convertir des notes vers un autre type</a>
                                <a class="dropdown-item" wire:click="insertClasseParticipateMarks"  tabindex="-1" href="#">Insérer des notes de Participation de classe</a>
                            @endif
                            <a class="dropdown-item" wire:click="createNewClasse" tabindex="-1" href="#">Créer une classe</a>
                            <a class="dropdown-item" wire:click="editClasseGroup({{$classe->id}})" tabindex="-1" href="#">Modifier la promotion</a>
                            @if($not_stopped)
                                <a wire:click="editClasseSubjects({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Définir les matières</a>
                                <a wire:click="settingsOnMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Effectuer une opération sur les notes</a>
                                <a wire:click="restorMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Restaurer des notes de classe</a>
                                @if(session()->has('classe_subject_selected') && session('classe_subject_selected') && $classe->hasNullsMarks(session('semestre_selected'), null, session('classe_subject_selected')) )
                                    <a wire:click="deleteNullMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Supprimer les notes zéros</a>
                                    <a wire:click="desactivateNullMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Désactiver les zéros</a>
                                    <a wire:click="normalizeNullMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Normaliser les notes zéros</a><a wire:click="activateNullMarks({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Activer les zéros</a>
                                @endif
                            @endif
                            <a class="dropdown-item" title="Imprimer les notes formatées en format Excel" wire:click="printSingleMarksAsExcelFile" tabindex="-1" href="#">Imprimer notes formatter en format excel</a>
                            <a class="dropdown-item" title="Imprimer les toutes les notes en format Excel" wire:click="printMarksAsExcelFile" tabindex="-1" href="#">Imprimer toutes les notes en format excel</a>
                            <a wire:click="throwPresence({{$classe->id}})"  class="dropdown-item" tabindex="-1" href="#">Faire la présence de la classe</a>
                            <div class="dropdown-divider"></div>
                            <a title="Recharger les données personnelles de la classe" wire:click="relaoadClassePersoDataPositionAndFilial" class="dropdown-item" tabindex="-1" href="#">Recharger la classe</a>
                          </div>
                        </li>

                    </ul>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <hr class="bg-secondary w-100 m-0 p-0">
                    <h5 class="cursor-pointer w-100 text-orange m-0 p-0 py-2 ">Informations Générales {{ $classe ? 'de la ' . $classe->name : "" }}</h5>
                    <hr class="bg-secondary w-100 m-0 p-0 mb-2">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card" href="#tab_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="fa fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Effectif 
                                        (<b class="text-warning">
                                            {{ $classe ? count($classe->getClasseCurrentPupils(null, true)) : 'vide'}}
                                        </b> <small>dont</small> <span class="text-warning">{{ count($classe->getAbandonneds()) }} </span> abds)
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
                                <span wire:click="to_print" class="info-box-icon"><i class="far z-scale cursor-pointer fa-heart"></i></span>
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
                                    <span class="info-box-number">
                                        @if($classe && $classe->getTimePlanInsertPourcentage())
                                            {{ $classe->getTimePlanInsertPourcentage() }}%
                                        @else
                                            <small class="text-white-50 font-italic">Données indisponibles</small>
                                        @endif
                                    </span>
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
                                    <h5 class="card-title cursor-pointer text-white-50" data-card-widget="collapse">Listes des enseignants | PP | Responsables de la classe </h5>
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
                                        <div class="w-100 p-0 m-0">
                                            <div class="card active" href="#tab_1" data-toggle="tab">
                                                @php
                                                    $teachers = $classe->getClasseCurrentTeachers();

                                                @endphp

                                                @if($teachers && count($teachers) > 0)

                                                    @foreach($teachers as $tt)

                                                        @if($tt)

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
                                                                    @foreach($tt->getTeacherTodayCourses($classe->id) as $tp)
                                                                        <small class="text-white-50 font-italic pt-1">
                                                                            {{ $tp }}
                                                                        </small>
                                                                    @endforeach
                                                                </span>
                                                            </div>

                                                        @endif
                                                        <hr class="bg-secondary text-secondary m-0 p-0 w-100">

                                                    @endforeach
                                                @else

                                                    <span class="p-2 text-warning float-right text-right">La liste est vide : Aucun enseignant n'a encore été désigné pour tenir cette classe!</span>

                                                @endif

                                                
                                            </div>
                                            
                                        </div>
                                        
                                    </div>

                                    <div class="m-0 w-100 p-0 mt-3">

                                        <hr class="bg-secondary w-100 m-0 p-0">
                                        <h6 class="m-0 text-white-50 py-1">Le Prof Principal et les Responsables de la classe</h6>
                                        <hr class="bg-secondary w-100 m-0 p-0">

                                        @if($classe)
                                            <div class="col-12 mx-auto p-0 m-0">

                                                @php

                                                    $pp = $classe->currentPrincipal();

                                                @endphp

                                                @if($pp)
                                                    <h6 class="text-white-50 p-1 w-100"> 
                                                        <span>Le prof principal actuel de cette classe est : </span>
                                                        <span class="text-warning">{{ $pp->getName() }}</span>
                                                        <span wire:click="editClasseReferee({{$classe->id}})" title="Editer le PP de la classe"class="text-primary ml-3 z-scale cursor-pointer">
                                                                <span class="text-primary cursor-pointer fa fa-edit fx-20 py-2 px-2"></span>
                                                        </span>
                                                    </h6>
                                                @else
                                                    <h6 class="text-warning p-1"> 
                                                        <span>Cette classe n'a pas encore de prof principal</span>

                                                        <span wire:click="editClasseReferee({{$classe->id}})" title="Définir le PP de la classe"class="text-primary ml-3 z-scale cursor-pointer">
                                                                <span class="text-primary cursor-pointer fa fa-edit fx-20 py-2 px-2"></span>
                                                        </span>
                                                    </h6>
                                                @endif

                                                <div class="d-flex w-100 justify-content-between">
                                                    <div>
                                                        @php

                                                            $rp1 = $classe->pupil_respo1();

                                                        @endphp

                                                        @if($rp1)
                                                            <h6 class="text-white-50 p-1 w-100"> 
                                                                <span>Le premier responsable actuel de cette classe est : </span>
                                                                <a title="Cliquer pour charger le profil de {{$rp1->getName()}}" class="" href="{{route('pupil_profil', ['id' => $rp1->id])}}">
                                                                    <span class="text-warning">{{ $rp1->getName() }}</span>
                                                                </a>
                                                                <span wire:click="editClasseRespo1({{$classe->id}})" title="Editer"class="text-danger ml-3 z-scale cursor-pointer">
                                                                    <span class="text-primary cursor-pointer fa fa-edit fx-20 py-2 px-2"></span>
                                                                </span>
                                                                
                                                            </h6>
                                                        @else
                                                            <h6 class="text-warning p-1 rounded"> 
                                                                <span>Cette classe n'a pas encore de premier responsable</span>
                                                                <span wire:click="editClasseRespo1({{$classe->id}})" title="Définir le premier responsable de la classe"class="text-danger ml-3 z-scale cursor-pointer">
                                                                    <span class="text-primary cursor-pointer fa fa-edit fx-20 py-2 px-2"></span>
                                                                </span>
                                                            </h6>
                                                        @endif
                                                    </div>

                                                    <div>
                                                        @php

                                                            $rp2 = $classe->pupil_respo2();

                                                        @endphp

                                                        @if($rp2)
                                                            <h6 class="text-white-50 p-1 w-100"> 
                                                                <span>Le second responsable actuel de cette classe est : </span>
                                                                <a title="Cliquer pour charger le profil de {{$rp2->getName()}}" class="" href="{{route('pupil_profil', ['id' => $rp2->id])}}">
                                                                    <span class="text-warning">{{ $rp2->getName() }}</span>
                                                                </a>
                                                                <span wire:click="editClasseRespo2({{$classe->id}})" title="Editer"class="text-danger ml-3 z-scale cursor-pointer">
                                                                    <span class="text-primary cursor-pointer fa fa-edit fx-20 py-2 px-2"></span>
                                                                </span>
                                                            </h6>
                                                        @else
                                                            <h6 class="text-warning p-1 rounded"> 
                                                                <span>Cette classe n'a pas encore de second responsable</span>
                                                                <span wire:click="editClasseRespo2({{$classe->id}})" title="Definir le second responsable de la classe"class="text-danger ml-3 z-scale cursor-pointer">
                                                                    <span class="text-primary cursor-pointer fa fa-edit fx-20 py-2 px-2"></span>
                                                                </span>
                                                            </h6>
                                                        @endif

                                                    </div>

                                                </div>

                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif


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
                <h3 class="card-title d-flex p-3 w-100">
                      
                    @if (!$editingClasseName)
                        <div class="m-0 p-0 px-2 d-flex">
                            <form class="d-inline" action="">
                                @csrf()
                                <select id="semestre_selected" wire:model="semestre_selected" class="form-select custom-select border border-warning">
                                  <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                    @foreach ($semestres as $semestre)
                                        <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                    @endforeach
                                </select>
                            </form>
                            <form class="d-inline mx-2 " action="">
                                @csrf()
                                <select style="letter-spacing: 1.2px;" wire:model="section_selected" class="form-select text-uppercase font-weight-bold border border-orange custom-select ml-3">
                                  <option value="{{null}}">Veuillez sélectionner une section</option>
                                    @foreach ($sections as $value => $section)
                                        <option class="" value="{{$value}}">{{$section}}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif
                    <div class="d-inline-block float-right ml-auto">
                        @if ($classe && !$editingClasseName)
                            <span class="text-orange mx-1">
                                @if($classe)
                                    @php
                                        $cl = $classe->getNumericName();
                                    @endphp
                                    <span class="fa fa-2x ">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </span>
                                @else
                                    <span>Classe inconnue</span>
                                @endif
                            </span>
                            <span wire:click="editClasseName({{$classe->id}})" class="fa fa-edit cursor-pointer mx-2"></span>

                        @elseif($editingClasseName)
                            <form wire:submit.prevent="updateClasseName" autocomplete="off" class="my-1 d-flex p-2 justify-content-end cursor-pointer shadow border border-secondary">
                                <div class="d-flex justify-between zw-80">
                                  <div class="w-100">
                                    <x-z-input :type="'text'" :error="$errors->first('classeName')" :modelName="'classeName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                  </div>
                                </div>
                                <div class="mt-3 ml-2">
                                    <span title="Valider la mise à jour du nom de la classe" wire:click="updateClasseName" class="d-flex justify-content-between btn btn-primary px-3 cursor-pointer border">
                                        <span class="fa fa-check mt-1 mr-2"></span>
                                        <span>OK</span>
                                  </span>
                                </div>
                                <div class="d-inline-block float-right text-right zw-20">
                                  <span wire:click="cancelEditingName" title="Fermer la fenêtre d'édition" class="fa cursor-pointer text-danger p-2">X</span>
                                </div>
                            </form>
                        @endif
                    </div>
                </h3>
                @if($classe || $classeSelf)

                    

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
                    <div class="">

                        <div>
                            @livewire('progress-bar-small-component', ['classe_id' => $classe->id])
                        </div>

                        @if($section_selected == 'liste')

                            @livewire('classe-pupils-lister', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'marks')

                            @livewire('classe-marks-lister', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'classe_marks_updating_requests')

                            @livewire('pupil-marks-updating-by-teacher-component', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'classe_marks_insertion_progress')

                            @livewire('pupils-marks-insertion-progress-component', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'excel_files')

                            @livewire('classes-marks-excel-files-completed', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'averages')

                            @livewire('classe-averages-component', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'simple_classe_marks_view')

                            @livewire('classe-pupils-marks-lister-formated', ['classe_id' => $classe->id])
                        
                        @elseif($section_selected == 'related_marks')

                            @livewire('classe-pupil-related-mark', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'lates_absences')

                            @livewire('classe-presence-absence', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'classe_general_stats')

                            @livewire('classe-generals-stats', ['classe_id' => $classe->id])

                        @elseif($section_selected == 'time_plan')
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
                        @else


                        @endif
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
