<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="card container-fluid m-0 p-0 w-100 bg-transparent border border-dark p-3">
            <div class="card-header bg-dark"> 
                <h5 class="card-title cursor-pointer" data-card-widget="collapse"></h5>
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
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning py-2">
                <h6 class="m-0 p-0 h6 text-white-50">
                    Liste complète des apprenant du <span class="text-warning">{{$level ? $level->nameInFrench() : ''}}</span> enregistrés sur la plateforme <span class="text-warning"></span>

                    <span class="float-right text-muted"> </span>
                </h6>
            </blockquote>
        </div>
        <div class="w-100 mx-auto p-3">
            
                <div class="w-100 my-1 mt-2 d-flex justify-content-between">
                    @if(auth()->user()->isAdminAs('master'))
                        <div>
                            <span wire:click="multiplePupilInsertions" class="btn bg-orange border border-white" title="Ajouter des aprrenants à cette classe">
                                <span class="fa fa-user-plus"></span>
                                <span>Multiple Ajout</span>
                            </span> 
                            <span wire:click="addNewPupilTo" class="btn btn-primary border border-white" title="Ajouter un aprrenant à cette classe">
                                <span class="fa fa-user-plus"></span>
                                <span>Ajouter</span>
                            </span>
                            <span wire:click="editClasseSubjects"  class="btn mx-2 btn-secondary border border-white" title="Editer les matières de cette classe">
                                <span class="fa fa-edit"></span>
                                <span>Editer</span>
                            </span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-start m-0 p-0">
                        <span class="nav-item">
                            <select wire:model="sexe_selected" class="form-select z-bg-secondary custom-select">
                                <option value=""> Les apprenants par sexe </option>
                                <option value="male"> Garçons </option>
                                <option value="female"> Filles </option>
                            </select>
                        </span>

                        <span class="nav-item mx-2">
                            <select wire:model="classe_group_id_selected" class="form-select z-bg-secondary custom-select">
                                <option value=""> Les apprenants par promotion </option>
                                @foreach($classe_groups as $classe_group)
                                    <option value="{{$classe_group->id}}"> Promotion {{ $classe_group->name }} </option>
                                @endforeach
                            </select>
                        </span>

                        <span class="nav-item mx-2">
                            <select wire:model="classe_id_selected" class="form-select z-bg-secondary custom-select">
                                <option value=""> Les apprenants par classe </option>
                                @foreach($classes as $cl)
                                    <option value="{{$cl->id}}"> La {{ $cl->name }} </option>
                                @endforeach
                            </select>
                        </span>
                    </div>

                    @if($pupils && count($pupils))
                    <span wire:click="valideSemestre" class="btn mx-2 btn-info border border-white float-right" title="Imprimer la liste de complète...">
                        <span class="fa fa-print"></span>
                        <span>Impr.</span>
                    </span>
                    @endif
                </div>
                <div class="w-100 m-0 p-0 mt-3">
                @if($pupils && count($pupils) > 0)
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                    <thead class="text-white text-center">
                        <th class="py-2 text-center">#ID</th>
                        <th class="">Nom et Prénoms (Sexe)</th>
                        <th class="">Matricule</th>
                        <th>Pré-Classe(An.Sco)</th>
                        <th>Classe actuelle</th>
                        <th>Moy. en Pré-classe</th>
                        <th>Inscrit(e)</th>
                        <th>Action</th>
                        <th>Suppr.</th>
                    </thead>
                    <tbody>
                        @foreach($pupils as $k => $p)
                            <tr class="">
                                <td class="text-center border-right">{{ $loop->iteration }}</td>
                                <td class="text-capitalize pl-2">
                                    <span class="d-flex w-100">
                                        @if (!$editingPupilName && $p->id !== $pupil_id)
                                        <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                            <span class="d-flex">
                                                <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                                    {{$p->getName()}}
                                                </span>
                                                <span class="ml-3">({{$p->getSexe()}})</span>
                                            </span>
                                        </a>
                                        @endif
                                        @if($editingPupilName && $p->id == $pupil_id)
                                            <form wire:submit.prevent="updatePupilName" autocomplete="off" class="my-0 d-flex p-1 cursor-pointer w-100 shadow border border-secondary">
                                            <div class="d-flex justify-between px-1 w-100 row">
                                                <div class="col-9 d-flex justify-content-between row m-0 p-0 px-1">
                                                    <x-z-input :width="'col-5'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilFirstName')" :modelName="'pupilFirstName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                                    <x-z-input :width="'col-6'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilLastName')" :modelName="'pupilLastName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                                </div>

                                                <div class="col-2 m-0 p-0 d-flex row">
                                                    <span wire:click="updatePupilName" class="btn w-100 d-inline-block btn-primary border p-0 cursor-pointer">
                                                        OK
                                                    </span>
                                                </div>
                                            </div>
                                            </form> 
                                        @endif
                                        @if (!$editingPupilName)
                                            <span title="Editer le nom de l'apprenant {{$p->firstName . ' ' . $p->lastName}}" wire:click="editPupilName({{$p->id}})" class="fa bi-pen cursor-pointer mx-2 float-right"></span>
                                        @endif
                                        @if($editingPupilName && $p->id == $pupil_id)
                                            <span wire:click="cancelEditingPupilName" title="Fermer la fenêtre d'édition" class="fa cursor-pointer text-danger mx-2 float-right">X</span>
                                        @endif
                                    </span>
                                </td>
                                <td wire:click="changePupilSexe({{$p->id}})" class="text-center cursor-pointer" title="Doublecliquer pour changer le sexe">
                                    {{ $p->matricule }}
                                </td>
                                @php
                                    $pre = $p->getPupilPreclasse();
                                    $preclasse = $pre['classe'];
                                    $school_year = $pre['school_year'];
                                    if($preclasse){
                                        $pcl = $preclasse->getNumericName();
                                    }
                                @endphp
                                <td class="text-center px-1">
                                    @if($preclasse)
                                    <span class="">
                                        {{ $pcl['root'] }}<sup>{{ $pcl['sup'] }} </sup> {{ $pcl['idc'] }}
                                    </span>
                                    <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year }})</small>
                                    @else
                                        Aucune <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year }})</small>
                                    @endif
                                </td>
                                @php
                                    $current_classe = $p->getCurrentClasse();
                                    if($current_classe){
                                        $cl = $current_classe->getNumericName();
                                    }
                                @endphp
                                <td class="text-center px-1">
                                    @if($current_classe)
                                    <span class="">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </span>
                                    <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_model->school_year }})</small>
                                    @else
                                        Aucune <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_model->school_year }})</small>
                                    @endif
                                </td>
                                <td class="px-2">{{  rand(9.55, 19.75) }}</td>
                                <td class="text-center">
                                    {{ $p->getDateAgoFormated(false) }}
                                </td>
                                @if(!$editingPupilName)
                                    <td class="text-center w-auto p-0">
                                        <span class="row w-100 m-0 p-0">
                                            @if ($p->inPolyvalenceClasse())
                                                <span title="Définir la classe de  l'apprenant {{$p->name}}" wire:click="classed({{$p->id}})" class="text-danger col-12 m-0 p-0 cursor-pointer">
                                                    <span class="text-primary cursor-pointer fa bi-tools py-2 px-2"></span>
                                                </span>
                                            @else
                                                <span title="Renvoyer l'apprenant {{$p->name}} en classe volante ou polyvalence" wire:click="unclassed({{$p->id}})" class="text-danger col-3 m-0 p-0 cursor-pointer">
                                                    <span class="text-orange cursor-pointer fa bi-tools py-2 px-2"></span>
                                                </span>

                                                @if($p->canUpdateMarksOfThisPupil())
                                                    <span title="Verrouiller édtion des notes de l'apprenant {{$p->name}}" wire:click="lockMarksUpdating({{$p->id}})" class="text-warning border-right border-left col-3 m-0 p-0 cursor-pointer">
                                                        <span class="cursor-pointer fa fa-lock py-2 px-2"></span>
                                                    </span>
                                                @else
                                                    <span title="déverrouiller édtion des notes de l'apprenant {{$p->name}}" wire:click="unlockMarksUpdating({{$p->id}})" class="text-warning border-right border-left col-3 m-0 p-0 cursor-pointer">
                                                        <span class="cursor-pointer fa fa-unlock py-2 px-2"></span>
                                                    </span>

                                                @endif

                                                @if($p->canInsertOrUpdateMarksOfThisPupil())
                                                    <span wire:click="lockMarksInsertion({{$p->id}})" title="Verrouiller la gestion des notes de l'apprenant {{$p->name}}" class="text-info col-3 m-0 p-0 cursor-pointer border-right">
                                                        <span class="fa fa-lock py-2 px-2"></span>
                                                    </span>

                                                @else
                                                    <span wire:click="unlockMarksInsertion({{$p->id}})" title="déverrouiller la gestion des notes de l'apprenant {{$p->name}}" class="text-info col-3 m-0 p-0 cursor-pointer border-right">
                                                        <span class="fa fa-unlock py-2 px-2"></span>
                                                    </span>
                                                @endif
                                                <span title="Faire migrer l'apprenant {{$p->name}}" class="text-success col-3 m-0 p-0 cursor-pointer">
                                                    <span class="fa fa-recycle py-2 px-2"></span>
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span wire:click="deletePupil({{$p->id}})" title="Supprimer définitivement l'apprenant {{$p->name}}" class="text-danger m-0 p-0 cursor-pointer">
                                            <span class="fa fa-trash py-2 px-2"></span>
                                        </span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>   
                @endif
                @if(!$pupils)
                    <div class="my-2 p-2 text-center border rounded text-white-50">
                        <h6 class="mx-auto p-3 text-white-50">
                            <h1 class="m-0 p-0">
                                <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                            </h1>
                            <span class="text-warning">Aucun apprenant n'a été trouvé!</span> 
                            pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> pour les diverses sélections effectuées!
                            

                            <blockquote class="text-info">
                                Veuillez essayer une autre sélection
                            </blockquote>
                        </h6>
                    </div>
                @endif
        </div>
        </div>


    </div>


</div>