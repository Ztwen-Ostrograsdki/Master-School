<div>
    <div class="px-2">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse">Informations Générales de la
                    <span class="text-warning">
                        {{ $classe_group ? ' Promotion ' . $classe_group->name : "" }}
                    </span>

                </h5>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            </div>
            <div class="card-body">
                <div class="container-fluid m-0 p-0 w-100">
                    <div class="card-deck w-100 p-0 m-0">
                        <div class="card active" href="#tab_classe_group_1" data-toggle="tab">
                            <div class="info-box m-0 p-0 bg-info">
                                <span class="info-box-icon"><i class="fa fa-user-friends"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Effectif</span>
                                    <span class="info-box-number"></span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-primary">
                                <span class="info-box-icon"><i class="fa fa-user-nurse"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Les Notes</span>
                                    <span class="info-box-number">90 000</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-success">
                                <span class="info-box-icon"><i class="far fa-heart"></i></span>
                                <div class="info-box-content">
                                  <span class="info-box-text">Scolarités</span>
                                  <span class="info-box-number">92 050</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="info-box m-0 p-0 bg-danger">
                                <span class="info-box-icon"><i class="fa fa-cloud-download-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Emploi du temps</span>
                                    <span class="info-box-number">114 381</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3 mx-2">
        <div class="card-header d-flex p-0">
            <h3 class="card-title">
                <ul class="nav nav-pills ml-auto p-2">
                    <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                        Reglages <span class="caret"></span>
                      </a>
                      <div class="dropdown-menu">
                        <a class="dropdown-item" wire:click="deleteAllPupil({{$classe_group->id}})" tabindex="-1" href="#">Rafraichir la classe</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Vider toutes les notes</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Rafraichir les notes</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Rafraichir les absences</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Rafraichir les retards</a>
                        <a class="dropdown-item" tabindex="-1" href="#">Mettre à jour</a>
                        <a class="dropdown-item" wire:click="createNewClasseGroup" tabindex="-1" href="#">Créer une promotion</a>
                        <a class="dropdown-item" wire:click="createNewClasse" tabindex="-1" href="#">Créer une classe</a>
                        <a class="dropdown-item" wire:click="editClasseGroup({{$classe_group->id}})" tabindex="-1" href="#">Modifier la promotion</a>
                        <a wire:click="editClasseSubjects({{$classe_group->id}})"  class="dropdown-item" tabindex="-1" href="#">Définir les coeficients</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" tabindex="-1" href="#">Autres</a>
                      </div>
                    </li>
                    <li wire:click="setClasseGroupProfilActiveSection('liste')" class="nav-item"><a class="nav-link @if(session()->has('classe_group_profil_section_selected') && session('classe_group_profil_section_selected') == 'liste') active @elseif(!session()->has('classe_group_profil_section_selected')) active @endif border border-white" href="#tab_classe_group_1" data-toggle="tab">Liste</a>
                    </li>
                    <li wire:click="setClasseGroupProfilActiveSection('coefs')" class="nav-item"><a class="nav-link @if(session()->has('classe_group_profil_section_selected') && session('classe_group_profil_section_selected') && session('classe_group_profil_section_selected') == 'coefs') active @endif border border-white mx-1" href="#tab_classe_group_2" data-toggle="tab">Gestion des coéfiscients</a>
                    </li>
                    <li class=" float-right">
                        <span class="justify-content-between">
                            <span class="float-right btn btn-primary mr-2 border">
                                <span class="bi-trash text-orange"></span>
                                <span class="ml-1">Vider</span>
                            </span>
                        </span>
                    </li>
                    <li class=" float-right">
                        <span wire:click="addNewsClassesToThisClasseGroup({{$classe_group->id}})" title="Ajouter un groupe pédagogique à cette promotion de {{$classe_group->name}}" class="float-right btn btn-success mr-2 border">
                            <span class="ml-1 text-dark">
                                <span class="fa fa-plus"></span>
                                <span class="bi-filter"></span>
                            </span>
                        </span>
                    </li>
                    <li class=" float-right">
                        <span wire:click="addNewsSubjectsToThisClasseGroup({{$classe_group->id}})" title="Ajouter des matières à cette promotion de {{$classe_group->name}}" class="float-right btn btn-primary mr-2 border">
                            <span class="ml-1 text-dark">
                                <span class="fa fa-plus"></span>
                                <span class="bi-pen"></span>
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </h3>
    </div>

    @if($classe_group)
        <div class="card-body p-0 m-0">
          <div class="tab-content">
            <div class="m-0 p-0">
                <blockquote class="text-info">
                    <h6 class="m-0 p-0 h6 py-1 text-white-50 shadow d-flex justify-content-between">
                        <span class="pl-2">
                            Listes des groupes pédagogiques de la promotion <span class="text-warning">{{ $classe_group->name }} </span> au cours de l'année scolaire {{ session('school_year_selected')}}
                        </span>
                    </h6>
                </blockquote>
            </div>
            @if($classe_group)
            <div class="tab-pane la-liste-de-la-classe-promotion @if(session()->has('classe_group_profil_section_selected') && session('classe_group_profil_section_selected') == 'liste') active @elseif(!session()->has('classe_group_profil_section_selected')) active @endif" id="tab_classe_group_1">
                @livewire('classe-group-classe-liste', ['classe_group_id' => $classe_group->id])
            </div>
            <div class="tab-pane les-notes-de-la-classe @if(session()->has('classe_group_profil_section_selected') && session('classe_group_profil_section_selected') && session('classe_group_profil_section_selected') == 'coefs') active @endif" id="tab_classe_group_2">
                @livewire('classe-group-coef-liste', ['classe_group_id' => $classe_group->id])
            </div>
            @endif
          </div>
        </div>
    @endif
</div>
