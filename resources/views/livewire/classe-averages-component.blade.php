<div class="w-100 p-0 m-0">
    <div class="w-100 m-0 p-0 mx-auto">
        <div class="m-0 p-0 w-100">
            <blockquote class="text-warning py-2">
                Moyennes des apprenants de la classe 
            </blockquote>
            <hr class="text-warning w-100 m-0 p-0 bg-warning">
        </div>
        <div class="w-100 mx-auto p-3">
            
            <div class="w-100 my-1 mt-2 d-flex justify-content-start">
                <div class="d-flex justify-content-start m-0 p-0">
                    <span class="nav-item">
                        <select wire:model="sexe_selected" class="form-select z-bg-secondary custom-select">
                            <option value=""> Les apprenants par sexe </option>
                            <option value="male"> Garçons </option>
                            <option value="female"> Filles </option>
                        </select>
                    </span>
                    
                </div>

                @if($pupils && count($pupils))
                <span class="btn mx-2 btn-info border border-white float-right" title="Imprimer la liste de complète...">
                    <span class="fa fa-print"></span>
                    <span>Impr.</span>
                </span>
                <span class="btn mx-2 bg-orange border border-white float-right px-2 z-scale" title="Générer et imprimer les bulletins de la classe...">
                    <span class="fa fa-download"></span>
                    <span>Bulletins</span>
                </span>

                @if($classe && is_object($classe))
                    <span wire:click="optimizeClasseAveragesIntoDatabase({{$classe->id}})" class="btn mx-2 bg-success border border-white float-right px-2 z-scale" title="Recharger les moyennes de classe...">
                        <span class="fa fa-recycle"></span>
                        <span>Recharger</span>
                    </span>
                @endif

                <span wire:click="refreshOrder" class="btn {{(!$order && !$targetToOrder) ? 'd-none' : ''}} mx-2 btn-warning border border-white float-right px-2 z-scale" title="Ordonner par liste alphabétique...">
                    <span class="fa fa-filter"></span>
                    <span>A - Z</span>
                </span>
                @endif
            </div>
            @if($is_loading)
            <div class="w-100 d-flex justify-content-center flex-column">
                @livewire('loader-component')  
            </div>
            @else
            <div class="w-100 m-0 p-0 mt-3">
                @if($pupils && count($pupils) > 0)

                    <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                        <col>
                        <col>
                        <col>
                        <col>
                        <colgroup span="{{count($semestres) + 1}}"></colgroup>
                        <colgroup span="3"></colgroup>
                        <col>
                        <col>
                        <tr class="text-center bg-secondary-light-1 ">
                            <th rowspan="2">No</th>
                            <th rowspan="2">Nom et Prénoms </th>
                            <th rowspan="2">N° EducMaster</th>
                            <th rowspan="2">Contacts</th>
                            <th colspan="{{count($semestres) + 1}}" scope="colgroup">Moyennes</th>
                            <th rowspan="2">Actions</th>
                            <th rowspan="2">Suppr.</th>
                        </tr>
                        <tr class="text-center bg-secondary-light-3">
                            @foreach($semestres as $s)
                                <th scope="col" class="py-1">
                                    {{$semestre_type . ' ' . $s }} 
                                    <span class="text-orange">(Rang)</span>
                                    <span wire:click="orderer('{{$s}}')" class="mx-2 border border-white rounded float-right px-2 z-scale {{ $targetToOrder == $s ? 'd-none' : '' }} " title="Ordonner par ordre de mérite les moyennes du {{$semestre_type . ' ' . $s}}...">
                                        <span class="fa fa-filter"></span>
                                    </span>
                                </th>
                            @endforeach
                            <th scope="col" class="py-1">
                                Générale <span class="text-orange">(Rang)</span>
                                <span wire:click="orderer" class="mx-2 border border-white rounded float-right px-2 z-scale {{($order && !$targetToOrder) ? 'd-none' : ''}} " title="Ordonner par ordre de mérite les moyennes générales">
                                        <span class="fa fa-filter"></span>
                                    </span>
                            </th>
                        </tr>

                        @foreach($pupils as $p)
                            <tr class="text-left text-center">
                                <th scope="row" class="text-center py-2">{{ $loop->iteration }}</th>
                                <th class="text-capitalize text-center pl-2 p-0 m-0">
                                    <span class="d-flex justify-content-between">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                            <a class="text-decoration-none @if($p->sexe == 'female') text-orange @else text-white @endif" href="{{route('pupil_profil', [$p->id])}}">
                                                {{ $p->getName() }}
                                            </a>
                                        </span>
                                        
                                    </span>
                                </th>
                                <th title="Numéro EducMaster" class="text-capitalize text-white-50 text-center p-0 m-0 px-2">
                                    {{$p->ltpk_matricule}}
                                </th>
                                <th style="letter-spacing: 1.2px;" class="text-capitalize text-center pl-2 p-0 m-0">
                                    {{$p->contacts}}
                                </th>
                               
                                @foreach($semestres as $sm)
                                        @php

                                            $semestrialAverage = $p->average($classe->id, $sm, $school_year_model->id);

                                           if($semestrialAverage){

                                                $moy_sm = $semestrialAverage->moy;

                                                $mention_sm = $semestrialAverage->mention;

                                                $min_sm = $semestrialAverage->min;

                                                $max_sm = $semestrialAverage->max;

                                                $rank_sm = $semestrialAverage->rank;

                                                $base_sm = $semestrialAverage->base;

                                                $exp_sm = $semestrialAverage->exp;

                                            }
                                            else{

                                                $semestrialAverage = null;

                                            }
                                        @endphp

                                    <th class="text-center pl-2 p-0 m-0">

                                        @if($semestrialAverage)
                                            <span class="{{$moy_sm >= 10 ? 'text-green-y' : 'text-danger'}}">
                                                {{ $moy_sm > 9 ? $moy_sm : '0' . $moy_sm }}
                                            </span>
                                            (<span class="text-warning">
                                                <span>{{$rank_sm}}</span><sup>{{$exp_sm}}</sup><small>{{$base_sm }} </small>
                                            </span>)
                                        @else
                                           <small class="font-italic text-white-50">Non classé</small>
                                        @endif
                                    </th>
                                @endforeach
                                <th>
                                @php 

                                    $annualAverage = $p->annual_average($classe->id, $school_year_model->id);

                                   if($annualAverage){

                                        $moy_an = $annualAverage->moy;

                                        $mention_an = $annualAverage->mention;

                                        $min_an = $annualAverage->min;

                                        $max_an = $annualAverage->max;

                                        $rank_an = $annualAverage->rank;

                                        $base_an = $annualAverage->base;

                                        $exp_an = $annualAverage->exp;

                                    }
                                    else{

                                        $annualAverage = null;

                                    }

                                @endphp 

                                    @if($annualAverage)
                                        <span class="{{$moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                            {{ $moy_an > 9 ? $moy_an : '0' . $moy_an }}
                                        </span>
                                        (<span class="text-warning">
                                            <span>{{$rank_an}}</span><sup>{{$exp_an}}</sup><small>{{$base_an }} </small>
                                        </span>)
                                    @else
                                       <small class="font-italic text-white-50">Non classé</small>
                                    @endif

                                </th>
                                    <th class="text-center w-auto p-0">
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
                                    </th>
                                    <th class="text-center">
                                        <span wire:click="forceDelete({{$p->id}})" title="Supprimer définitivement l'apprenant {{$p->name}}" class="text-danger m-0 p-0 cursor-pointer">
                                            <span class="fa fa-trash py-2 px-2"></span>
                                        </span>
                                    </th>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div class="my-2 p-2 text-center border rounded text-white-50">
                        <h6 class="mx-auto p-3 text-white-50">
                            <h1 class="m-0 p-0">
                                <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                            </h1>
                            <span class="text-warning">Aucun apprenant n'a été trouvé!</span> 
                            pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> pour les diverses sélections effectuées!
                            

                            <blockquote class="text-info">
                                Veuillez vérifier les données
                            </blockquote>
                        </h6>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>