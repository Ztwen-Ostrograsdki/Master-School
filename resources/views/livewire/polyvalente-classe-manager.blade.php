<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">

        <div class="card-header d-flex justify-content-between p-0 px-3 mx-3 border border-orange rounded">
            <span style="letter-spacing: 1.2px;" class="ml-3 mt-2">
               <span class="info-box-text">Effectif : 
                    <b class="text-warning">
                        {{ $classe ? count($classe->getPupils(session('school_year_selected'))) : 'vide'}}
                    </b>
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
            </span>
            <ul class="nav nav-pills ml-auto p-2">
                <span class="text-orange mx-1">
                    @if($classe)
                        @php
                            $cl = $classe->getNumericName();
                        @endphp
                        <span class="fa fa-2x underline">
                            {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                        </span>
                    @else
                        <span>Classe inconnue</span>
                    @endif
                </span>
            </ul>
        </div><!-- /.card-header -->
        <div class="card-body">
            <div class="tab-content">
                <div>
                    <blockquote class="text-warning">
                        <div class="d-flex justify-content-between">
                            <h6 class="m-0 p-0 h6 text-white-50 py-2">
                                Liste des apprenants de la <a class="text-warning underline" href="{{route('classe_profil', ['slug' => $classe->slug])}}">{{$classe->name}}</a> du {{$level->nameInFrench()}} de la plateforme <span class="text-warning"></span>
                            </h6>
                        
                        </div>
                    </blockquote>
                </div>
            </div>

        </div>

        <div class="w-100 mx-auto p-3">
            @if($classe)
                <div class="w-100 my-1 mt-2">
                    @if(auth()->user()->isAdminAs('master'))
                        <span wire:click="multiplePupilInsertions" class="btn bg-orange border border-white" title="Ajouter des aprrenants à cette classe">
                            <span class="fa fa-user-plus"></span>
                            <span>Multiple Ajout</span>
                        </span> 
                        <span wire:click="addNewPupilTo" class="btn btn-primary border border-white" title="Ajouter un aprrenant à cette classe">
                            <span class="fa fa-user-plus"></span>
                            <span>Ajouter</span>
                        </span>
                    @endif
                    @if($pupils && count($pupils))
                    <a href="{{route('classe_pdf', $classe->id)}}"  class="btn mx-2 btn-info border border-white float-right" title="Imprimer la liste de cette classe...">
                        <span class="fa fa-print"></span>
                        <span>Impr.</span>
                    </a>
                    @endif
                </div>
                <div class="w-100 m-0 p-0 mt-3">
                @if($pupils && count($pupils) > 0)
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                    <thead class="text-white text-center">
                        <th class="py-2 text-center">#ID</th>
                        <th class="">Nom et Prénoms (Sexe)</th>
                        <th class="">Matricule</th>
                        <th>Dernière classe faite (An.Sco)</th>
                        <th>Moy. en Pré-classe</th>
                        <th>En polyvalence depuis</th>
                        <th>Action</th>
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
                                    $lastClasse = $p->getLastClasse();

                                    if($lastClasse){

                                        $classe = $lastClasse['classe'];

                                        $school_year = $lastClasse['school_year'];

                                        if($classe){

                                            $cl = $classe->getNumericName();
                                        }

                                    }
                                    else{

                                        $lastClasse = null;

                                        $classe = null;

                                    }

                                @endphp
                                <td class="text-center px-1">
                                    @if($classe)
                                    <span class="">
                                        {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                    </span>
                                    <small class="text-white-50 font-italic ml-2">({{ 'en ' . $school_year->school_year }})</small>
                                    @else
                                        <small class="text-white-50 font-italic ml-2">Aucune classe faite</small>
                                    @endif
                                </td>
                                
                                @if($lastClasse && $classe && $school_year)

                                    @php

                                        $annualAverage = $p->annual_average($classe->id, $school_year->id);

                                        if($annualAverage && is_object($annualAverage)){

                                            $moy_an = $annualAverage->moy;

                                        }
                                        else{

                                            $annualAverage = null;

                                        }

                                    @endphp
                                    <td class="px-2">
                                        @if($annualAverage)
                                            <span class="mr-2 {{$moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                                {{ $moy_an > 9 ? $moy_an : '0' . $moy_an }}
                                            </span>
                                            (<span class="text-orange mx-1">
                                                <span>{{$annualAverage->rank}}</span><sup>{{$annualAverage->exp}}</sup><small>{{$annualAverage->base }} </small>
                                            </span>)
                                        @else

                                        @endif
                                    </td>

                                @else
                                    <td class="text-center px-2">
                                        <small class="text-white-50 font-italic ml-2">Non défini</small>
                                    </td>
                                @endif

                                <td class="text-center">
                                    @if($p->inPolyvalenceClasseSince())
                                        {{ $p->inPolyvalenceClasseSince() }}
                                    @else
                                        <small class="font-italic text-white-50">Date inconnue </small>
                                    @endif
                                </td>
                                @if(!$editingPupilName)
                                    <td class="text-center w-auto p-0">
                                        <span class="row w-100 m-0 p-0">
                                            <span title="Supprimer définivement {{$p->getName()}} de la base de donnée" wire:click="forceDeletePupil({{$p->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                                <span class="text-danger cursor-pointer z-scale fa fa-trash py-2 px-2"></span>
                                            </span>
                                            <span title="Bloquer {{$p->getName()}}" class="text-info col-4 m-0 p-0 cursor-pointer border-left border-right">
                                                <span class="fa fa-lock py-2 z-scale px-2"></span>
                                            </span>
                                            <span wire:click="migrateTo({{$p->id}})" title="Faire migrer l'apprenant {{$p->getName()}}" class="text-success col-4 m-0 p-0 cursor-pointer">
                                                <span class="fa fa-recycle z-scale py-2 px-2"></span>
                                            </span>
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
                            Il parait qu'aucune donnée n'est disponible pour cette classe de 
                            <span class="text-warning">{{ $classe ? $classe->name : 'inconnue' }}</span> 
                            pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                            

                            <blockquote class="text-info">
                                Veuillez sectionner une autre année scolaire
                            </blockquote>
                        </h6>
                    </div>
                @endif
            @endif                                                
        </div>
        </div>


    </div>


</div>