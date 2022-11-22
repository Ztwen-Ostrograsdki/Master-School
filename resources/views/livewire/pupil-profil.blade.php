<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Informations Générales 
                    {{ $pupil ? " de l'apprenant " : ''}}  
                    <span class="text-warning">{{ $pupil ? $pupil->getName() : "" }}</span>
                </h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="card-tools mr-3">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item dropdown">
                          <a class="nav-link dropdown-toggle border border-primary" data-toggle="dropdown" href="#">
                            Reglages <span class="caret"></span>
                          </a>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" tabindex="-1" wire:click="resetMarks" href="#">Rafraichir les notes</a>
                            <a class="dropdown-item" wire:click="resetAbsences" tabindex="-1" href="#">Rafraichir les absences</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Rafraichir les notes relatives</a><a class="dropdown-item" wire:click="resetMarks" tabindex="-1" href="#">Rafraichir les retards</a>
                            <a class="dropdown-item" tabindex="-1" href="#">Mettre à jour</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                          </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="row d-flex">
                        <div class="col-md-3">
                            <div class="card card-success m-0 p-0">
                                <div class="card-header m-0">
                                    <h3 class="card-title m-0 p-0">Profil</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="card-refresh" data-source="{{route('pupil_profil', ['id' => $pupil->id])}}" data-source-selector="#card-refresh-content" data-load-on-init="false">
                                        <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                        <i class="fas fa-expand"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body m-0 p-0">
                                    <div class="m-0 p-0 rounded-bottom">
                                        <img width="600" class="border m-0 p-0 my-1" src="{{$pupil->__profil(500)}}" alt="photo de profil">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-4">
                            <div class="card card-info m-0 p-0 border border-primary">
                                <div class="card-header m-0">
                                    <h3 class="card-title">Infos personnelles</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" wire:click="editPupilPersoData">
                                        <i class="fas fa-edit"></i>
                                        </button><button type="button" class="btn btn-tool" data-card-widget="card-refresh" data-source="{{route('pupil_profil', ['id' => $pupil->id])}}" data-source-selector="#card-refresh-content" data-load-on-init="false">
                                        <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                        <i class="fas fa-expand"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <x-ztitle-liner :title="'Classe : '" :value="$pupil->classe->name"></x-ztitle-liner>
                                    <x-ztitle-liner :icon="'bi-person'" :useIcon="true" :value="$pupil->getName()" :smallTitle="'('. $pupil->matricule . ')'" ></x-ztitle-liner>
                                    <x-ztitle-liner :smallTitle=" '(à ' . $pupil->birth_city . ')'" classe="text-capitalize" :icon="'bi-calendar'" :useIcon="true" :value="$pupil->__getDateAsString($pupil->birth_day, null)"></x-ztitle-liner>
                                    <x-ztitle-liner :icon="'bi-phone'" :useIcon="true" :value="$pupil->contacts"></x-ztitle-liner>
                                    <x-ztitle-liner :icon="'bi-house'" :useIcon="true" :value="$pupil->residence"></x-ztitle-liner>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card card-warning m-0 p-0 border border-warning">
                                <div class="card-header m-0">
                                    <h3 class="card-title">Détails classe</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="card-refresh" data-source="{{route('pupil_profil', ['id' => $pupil->id])}}" data-source-selector="#card-refresh-content" data-load-on-init="false">
                                        <i class="fas fa-sync-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="maximize">
                                        <i class="fas fa-expand"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                        </button>
                                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                                        <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                   <x-ztitle-liner :smallTitle="'08 réussies'" :title="'Nbre de notes : '" :value="'14'"></x-ztitle-liner>
                                   <x-ztitle-liner :smallTitle="' / Meilleur : 18 - Faible : 14'" :title="'Matière phare : '" :value="'Anglais'"></x-ztitle-liner>
                                   <x-ztitle-liner :smallTitle="' / Meilleur : 11 - Faible : 01'" :title="'Matière archilles : '" :value="'PCT'"></x-ztitle-liner>
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
          <!-- Custom Tabs -->
          <div class="card">
            <div class="card-header d-flex p-0">
              <h3 class="card-title p-3">
                @if (!$editingPupilName)
                <select id="semestre_selected" wire:model="semestre_selected" wire:change="changeSemestre" class="form-select ml-3">
                  <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                  @foreach ($semestres as $semestre)
                      <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                  @endforeach
                </select>
                @endif
              </h3>
                @if($pupil)
                    <ul class="nav nav-pills ml-auto p-2">
                        <li class="nav-item dropdown">
                            <a class="nav-link border border-warning dropdown-toggle text-warning" data-toggle="dropdown" href="#">
                                Archives de {{ $pupil->getName() }} <span class="caret"></span>
                            </a>
                            <div class="dropdown-menu">
                                @if($pupil->getArchives())
                                    @foreach($pupil->getArchives() as $archive)
                                        <a class="dropdown-item" tabindex="-1" href="#"> 
                                            <span>{{ $archive['classe']->name }}</span>
                                            <small> en </small>
                                            <small class="text-warning">
                                                ({{ $archive['school_year']->school_year }})
                                            </small>
                                         </a>
                                    @endforeach
                                @endif
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="nav nav-pills ml-auto p-2">
                        <li wire:click="setPupilProfilActiveSection('marks')" class="nav-item">
                            <a class="nav-link border border-white mx-1 @if((session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'marks') || !session('pupil_profil_section_selected')) active @endif" href="#tab_1" data-toggle="tab">Notes
                            </a>
                        </li>
                        <li wire:click="setPupilProfilActiveSection('related_marks')" class="nav-item">
                            <a class="nav-link @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'related_marks') active @endif border border-white mx-1" href="#tab_2" data-toggle="tab">Sanctions - Bonus</a>
                        </li>
                        <li wire:click="setPupilProfilActiveSection('absences')" class="nav-item">
                            <a class="nav-link @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'absences') active @endif border border-white mx-1" href="#tab_3" data-toggle="tab">Absence</a>
                        </li>
                        <li wire:click="setPupilProfilActiveSection('lates')" class="nav-item">
                            <a class="nav-link @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'lates') active @endif border border-white mx-1" href="#tab_4" data-toggle="tab">Retards</a>
                        </li>
                    </ul>
                @else
                    <h3 class="card-title ml-auto p-3 float-right text-warning">
                        <span class="bi-lock mx-2"></span>
                        <span>L'apprenant <b class="text-orange" > {{ $pupil->getName() }} </b> n'a pas de données disponibles pour l'année scolaire <b class="text-orange">{{ session('school_year_selected') }} </b> </span>
                    </h3>
                @endif
            </div><!-- /.card-header -->
            <div class="card-body">
              <div class="tab-content">
                @if (!$joinedToThisYear)
                <div class="w-100 border rounded border-warning p-2 m-2">
                  <blockquote class="text-info">
                        <h6 class="h6">
                            <span class="text-white-50 h6">
                                Veuillez cliquer sur le bouton pour lier cet apprenant à l'année {{ session('school_year_selected') }}
                            </span>
                            <div class="row">
                                <span wire:click="joinPupilToSchoolYear" class="text-center btn-primary cursor-pointer border h6 p-2 m-2 col-6">
                                    Générer les données de cet apprenant pour l'année scolaire {{ session('school_year_selected') }}
                                </span>
                                @if($classes && count($classes) > 0)
                                <form class="col-5 mt-2" action="">
                                    <div class="w-100">
                                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('classe_id') text-danger border border-danger @enderror" wire:model.defer="classe_id" name="classe_id">
                                            <option class="" value="{{null}}">Choisissez la classe de l'apprenant en l'année scolaire {{ session('school_year_selected') }}</option>
                                            @foreach ($classes as $c)
                                                @if(!$c->alreadyJoinedToThisYear())
                                                    <option title="Vous ne pouvez pas sélectionner cette classe car elle n'est pas encore disponible en l'année scolaire {{ session('school_year_selected') }}" disabled  value="{{$c->id}}">{{$c->name}}</option>
                                                @else
                                                    <option  value="{{$c->id}}">{{$c->name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('classe_id')
                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                        @enderror
                                    </div>
                                </form>
                                @else
                                    <span class="text-warning float-right text-right mt-3">Aucune classe n'est disponible</span>
                                @endif
                            </div>
                        </h6>
                  </blockquote>
                </div>
                @endif
                @if($pupil && $joinedToThisYear)
                    <div class="tab-pane les-notes-de-eleve @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'marks') active @elseif(!session()->has('pupil_profil_section_selected')) active @endif" id="tab_1">
                        @livewire('pupil-marks-listing', ['pupil_id' => $pupil->id])
                    </div>
                    <div class="tab-pane les-sanctions-de-eleve @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'related_marks') active @endif" id="tab_2">
                        @livewire('pupil-related-marks', ['pupil_id' => $pupil->id])
                    </div>
                    <div class="tab-pane les-absences-de-eleve @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'absences') active @endif" id="tab_3">
                        @livewire('pupil-absences', ['pupil_id' => $pupil->id])
                    </div>
                    <div class="tab-pane les-retards-de-eleve @if(session()->has('pupil_profil_section_selected') && session('pupil_profil_section_selected') == 'lates') active @endif" id="tab_4">
                        @livewire('pupil-lates', ['pupil_id' => $pupil->id])
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
