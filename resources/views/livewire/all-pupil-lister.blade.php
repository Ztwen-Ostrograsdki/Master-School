<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning py-2">
                {{ count($pupils) }} apprenants 
            </blockquote>
            <hr class="text-warning w-100 m-0 p-0 bg-warning">
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
                        <th >Classe actuelle {{ $lastYear->school_year }}
                            @if($lastYear->id !== $school_year_model->id)
                                <br class="m-0 p-0">
                                <small class="text-orange">(Classe en {{$school_year_model->school_year}})</small>
                            @endif
                        </th>
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
                                        <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                            <span class="d-flex">
                                                <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                                    {{$p->getName()}}
                                                </span>
                                                <span class="ml-3">({{$p->getSexe()}})</span>

                                                <span class="ml-3 float-right text-right mt-1 {{$p->isPupilOfThisYear() ? "fa fa-calendar-check text-success" : "fa fa-calendar-times text-danger"}} "></span>
                                            </span>
                                        </a>
                                    </span>
                                </td>
                                <td wire:click="changePupilSexe({{$p->id}})" class="text-center cursor-pointer" title="Doublecliquer pour changer le sexe">
                                    {{ $p->matricule }}
                                </td>
                                
                                @php
                                    $current_classe = $p->getCurrentClasse(null, true);

                                    if($current_classe){

                                        $cl = $current_classe->getNumericName();
                                    }

                                @endphp
                                <td class="text-center px-1">
                                    @if($current_classe)
                                    <span class="">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </span>
                                    <small class="text-white-50 font-italic ml-2">({{ 'en ' . $lastYear->school_year }})</small>
                                    @else
                                        Aucune <small class="text-white-50 font-italic ml-2">({{ 'en ' . $lastYear->school_year }})</small>
                                    @endif


                                    @if($lastYear->id !== $school_year_model->id)

                                        @php
                                            $current_classe_in = $p->getCurrentClasse();

                                            if($current_classe_in){

                                                $cli = $current_classe_in->getNumericName();
                                            }

                                        @endphp
                                        <br class="m-0 p-0">
                                        <span class="text-orange small">
                                            @if($current_classe_in)
                                                <span class="">
                                                    {{ $cli['root'] }}<sup>{{ $cli['sup'] }} </sup> {{ $cli['idc'] }}
                                                </span>
                                                <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year_model->school_year }})</small>
                                            @else
                                                Aucune <small class="font-italic ml-2">({{ 'en ' . $school_year_model->school_year }})</small>
                                            @endif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $p->getDateAgoFormated(false) }}
                                </td>
                                <td class="text-center w-auto p-0">
                                    @if($p->isPupilOfThisYear() )  
                                        <span wire:click="disjoin({{$p->id}})" title="Supprimer les données de l'apprenant {{$p->name}} en rapport avec {{ $school_year_model->school_year }}" class="text-warning m-0 p-0 cursor-pointer">
                                            <span class="fa fa-trash py-2 px-2"></span>
                                        </span>
                                    @else
                                        <span wire:click="join({{$p->id}})" title="Générer les données de l'apprenant {{$p->name}} en rapport avec {{ $school_year_model->school_year }}" class="text-success m-0 p-0 cursor-pointer">
                                            <span class="fa fa-recycle py-2 px-2"></span>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span wire:click="deletePupil({{$p->id}})" title="Supprimer définitivement l'apprenant {{$p->name}}" class="text-danger m-0 p-0 cursor-pointer">
                                        <span class="fa fa-trash py-2 px-2"></span>
                                    </span>
                                </td>
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