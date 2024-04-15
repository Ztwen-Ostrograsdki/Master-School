<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning p-0 m-0 mx-3">
                <hr class="m-0 p-0 w-100 bg-primary">
                <h6 class="m-0 p-0 h6 text-white-50 p-3">
                    Liste complète des apprenant du <span class="text-warning">{{$level ? $level->nameInFrench() : ''}}</span> enregistrés sur la plateforme <span class="text-warning"></span>

                    <span class="float-right text-muted"> 
                        Données chargées: 
                        <span class="text-warning mx-2"> {{ count($pupils) }} sur {{ $total }} </span>
                    </span>
                </h6>
                <hr class="m-0 p-0 w-100 bg-primary">
            </blockquote>
        </div>
        <div class="w-100 mx-auto p-3">
            
                <div class="w-100 my-1 mt-2 d-flex justify-content-between">
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

                        <span class="nav-item mx-2">
                            <select wire:model="pupil_type_selected" class="form-select z-bg-secondary custom-select">
                                <option value="all"> Tous les apprenants </option>
                                <option value="abandonned"> Les abandons </option>
                                <option value="continued"> Les apprenants en situation régulière </option>
                                
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
                        <th>
                            Pré-Classe(An.Sco)
                            <small class="d-block">Classe précédente</small>
                        </th>
                        <th>Moy. en Pré-classe</th>
                        <th>Classe actuelle</th>
                        <th>Inscrit(e)</th>
                        <th>Action</th>
                        <th>Suppr.</th>
                    </thead>
                    <tbody>
                        @foreach($pupils as $k => $p)
                            <tr class="">
                                <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td @if($editingPupilName && $pupil_id == $p->id) colspan="8" @endif  class="text-capitalize pl-2">
                            <span class="d-flex w-100">
                                @if (!$editingPupilName && $p->id !== $pupil_id)
                                <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="d-flex">
                                        <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                                @endif
                                @if($editingPupilName && $p->id == $pupil_id)
                                    <form wire:submit.prevent="updatePupilName" autocomplete="off" class="my-0 d-flex p-1 cursor-pointer w-100 shadow table align-middle m-2">
                                    <div class="d-flex justify-between px-1 align-middle table w-100 row m-0 p-0">
                                        <div class="col-9 d-flex align-middle justify-content-between row m-0 p-0 px-1">
                                            <x-z-input :width="'col-5 m-0'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilFirstName')" :modelName="'pupilFirstName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                            <x-z-input :width="'col-6 m-0'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilLastName')" :modelName="'pupilLastName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                        </div>

                                        <div class="col-2 m-0 p-0 align-middle d-flex row">
                                            <span wire:click="updatePupilName" class="btn w-100 d-inline-block btn-primary table py-1 mt-1 border p-0 cursor-pointer">
                                                <span class="mt-2">
                                                    <span>OK</span>
                                                    <span class="fa fa-check"></span>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    </form> 
                                        @elseif($editingPupilName && $pupil_id !== $p->id)
                                        <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                            <span class="d-flex">
                                                <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                                    {{$p->getName()}}
                                                </span>
                                            </span>
                                        </a>
                                        @endif
                                        @if (!$editingPupilName)
                                            <span title="Editer le nom de l'apprenant {{$p->firstName . ' ' . $p->lastName}}" wire:click="editPupilName({{$p->id}})" class="fa bi-pen cursor-pointer mx-2 float-right"></span>
                                        @endif
                                        @if($editingPupilName && $p->id == $pupil_id)
                                            <span wire:click="cancelEditingPupilName" title="Fermer la fenêtre d'édition" class="fa cursor-pointer text-danger mx-2 p-2 m-2 float-right">X</span>
                                        @endif
                                    </span>
                                </td>

                                @if(!$editingPupilName || $editingPupilName && $pupil_id !== $p->id)
                                
                                    <td class="text-center cursor-pointer">
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

                                        if($preclasse){

                                            $annualAverage = $p->annual_average($preclasse->id, $school_year->id);
                                        }
                                        else{

                                            $annualAverage = null;

                                        }

                                        if($annualAverage && is_object($annualAverage)){

                                            $moy_an = $annualAverage->moy;

                                        }
                                        else{

                                            $annualAverage = null;

                                        }

                                    @endphp
                                    <td class="px-2 text-center">
                                        @if($annualAverage !== null)
                                            <span class="mr-2 {{$moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                                {{ $moy_an > 9 ? $moy_an : '0' . $moy_an }}
                                            </span>
                                            (<span>{{$annualAverage->rank}}</span><sup>{{$annualAverage->exp}}</sup><small>{{$annualAverage->base }} </small>)
                                        @else
                                            <small class="text-white-50 font-italic">Non classé ou indisponible</small>
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
                                    <td class="text-center">
                                        {{ $p->getDateAgoFormated(false) }}
                                    </td>
                                @endif
                                @if(!$editingPupilName || $editingPupilName && $pupil_id !== $p->id)
                                
                                    <td class="text-center w-auto p-0">
                                        <span class="row w-100 m-0 p-0">
                                            @if ($p->inPolyvalenceClasse())
                                                <span title="Définir la nouvelle classe de  l'apprenant {{$p->name}}" wire:click="migrateTo({{$p->id}})" class="text-danger col-12 m-0 p-0 cursor-pointer">
                                                    <span class="text-primary cursor-pointer fa bi-tools py-2 px-2"></span>
                                                </span>
                                            @else
                                                @if($p->canUpdateMarksOfThisPupil())
                                                    <span title="Verrouiller édtion des notes de l'apprenant {{$p->name}}" wire:click="lockMarksUpdating({{$p->id}})" class="text-danger border-right col-4 m-0 p-0 cursor-pointer">
                                                        <span class="cursor-pointer fa fa-lock py-2 px-2"></span>
                                                    </span>
                                                @else
                                                    <span title="déverrouiller édtion des notes de l'apprenant {{$p->name}}" wire:click="unlockMarksUpdating({{$p->id}})" class="text-success border-right col-4 m-0 p-0 cursor-pointer">
                                                        <span class="cursor-pointer fa fa-unlock py-2 px-2"></span>
                                                    </span>

                                                @endif

                                                @if($p->canInsertOrUpdateMarksOfThisPupil())
                                                    <span wire:click="lockMarksInsertion({{$p->id}})" title="Verrouiller la gestion des notes de l'apprenant {{$p->name}}" class="text-info col-4 m-0 p-0 cursor-pointer border-right">
                                                        <span class="fa fa-lock py-2 px-2"></span>
                                                    </span>

                                                @else
                                                    <span wire:click="unlockMarksInsertion({{$p->id}})" title="déverrouiller la gestion des notes de l'apprenant {{$p->name}}" class="text-info col-4 m-0 p-0 cursor-pointer border-right">
                                                        <span class="fa fa-unlock py-2 px-2"></span>
                                                    </span>
                                                @endif
                                                <span wire:click="migrateTo({{$p->id}})" title="Faire migrer l'apprenant {{$p->name}} vers une nouvelle classe" class="text-success col-4 m-0 p-0 cursor-pointer">
                                                    <span class="fa fa-recycle py-2 px-2"></span>
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span wire:click="forceDelete({{$p->id}})" title="Supprimer définitivement l'apprenant {{$p->name}}" class="text-danger m-0 p-0 cursor-pointer">
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