<div class="m-0 p-0 w-100 bg-secondary-light-0" style="min-height: 1600px;">
    <div class="z-justify-relative-top-120 w-100" style="width: 90%;" >
    <div style="" class="my-1 py-1 px-3">
        <div class="w-100 d-flex justify-content-end">
            <span class="btn btn-success border py-2 px-3 my-2 w-100">
                <span class="fx-20 letter-spacing-12">PROFIL ENFANT</span>
            </span>
        </div>
        <div class="w-100 d-flex justify-content-between">
            <div class="m-0 p-0 px-2 d-inline-block my-2">
                <form class="d-inline" action="">
                    @csrf()
                    <select id="school_year_selected" wire:model="school_year_selected" class="form-select custom-select border bg-secondary-light-0 text-white border-warning">
                      <option disabled value="{{null}}">Veuillez sélectionner une année-scolaire</option>
                        @foreach ($school_years as $p_school_year)
                            @php
                                $cur_classe = $pupil->getCurrentClasse($p_school_year->school_year);
                            @endphp
                            <option class="@if($pupil_school_years && in_array($p_school_year->id, $pupil_school_years)) bg-success @endif" value="{{$p_school_year->school_year}}"> Année-Scolaire {{ $p_school_year->school_year }} 
                                <small class="float-right text-warning text-right">
                                    @if($pupil_school_years && in_array($p_school_year->id, $pupil_school_years))
                                        @if(isset($cur_classe) && is_object($cur_classe) && $cur_classe && isset($cur_classe->name))
                                            ({{ $cur_classe->name }})
                                        @else
                                            (Classe inconnue)
                                        @endif
                                    @else
                                        (Auncune classe)
                                    @endif
                                </small>
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <span class="btn btn-success py-2 px-3 my-2">
                <span>Recharger les nouvelles données</span>
                <span class="fa fa-recycle text-warning"></span>
            </span>
        </div>
        <div class="w-100">
            <blockquote class="text-primary">
                <h5 class="m-0 h6 p-0 py-2 text-white-50">
                    Les détails sur les notes de <span class="underline text-orange">{{$pupil->getName()}}</span> année-scolaire {{session('school_year_selected')}} classe {{$pupil->getCurrentClasse($school_year_selected) ? $pupil->getCurrentClasse($school_year_selected)->name : 'Aucune classe assignée en' . $school_year_selected}}
                </h5>
            </blockquote>
        </div>
        {{-- {{ dd($pupil, $classe, $school_year_model, $school_year_selected); }} --}}
        @if($pupil && $classe && $school_year_model)
        <div class="w-100 m-0 p-0 mt-3">
            <div class="w-100 my-2">
                <span class="py-2 px-3 my-2">
                    <span class="text-orange fa fa-warning fx-25"></span>
                </span>
                <h6 class="p-2 text-orange letter-spacing-12">
                    Chers parents; la procedure de calcul des moyennes employées n'a pas été détaillée sur votre profil. Il est possible que vos calculs ne correspondent pas aux valeurs ici présentées! <br>
                    En effet, dans certaines matières, il est possible que le prof ait choisir les 2, ou 3 ou 4 meilleurs pour le calcule des moyennes d'interrogation de sa matière! <br>
                    Par ailleurs nous vous conseillons de cliquer sur le bouton <span class="text-warning">Recharger les nouvelles données</span> afin de charger les données récentes relatives aux notes de vos enfants! <br>

                    <span class="text-warning float-right p-2 d-block mb-1">Nous sommes disponibles pour toutes sugestions!!!</span>
                    <span class="d-block my-1"></span>
                </h6>

            </div>
            <div class="m-0 p-0 w-100 my-3 bg-secondary-light-0">
                <hr class="text-warning w-100 m-0 p-0 bg-primary">
                <blockquote class="text-warning px-2 py-1 m-0 bg-secondary-light-0">
                    <span style="letter-spacing: 1.2px" class="fx-20 bg-secondary-light-0 font-weight-bold text-uppercase font-italic">
                        Les notes interros/devoirs  par matière
                    </span>
                </blockquote>
                <div class="m-0 p-0 px-2 d-inline-block my-2">
                    <form class="d-inline" action="">
                        @csrf()
                        <select id="semestre_selected" wire:model="semestre_selected" class="form-select custom-select border bg-secondary-light-0 text-white border-warning">
                          <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                            @foreach ($semestres as $semestre)
                                <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <hr class="text-warning w-100 m-0 p-0 bg-primary">
            </div>
            @if($semestre_selected && $semestre_selected !== null)
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                    <col>
                    <col>
                    <col>
                    <colgroup span="{{$epeMaxLenght}}"></colgroup>
                    <colgroup span="{{$participMaxLenght}}"></colgroup>
                    <colgroup span="{{$devMaxLenght}}"></colgroup>
                    <colgroup span="3"></colgroup>
                    <col>
                    <tr class="text-center bg-secondary-light-0">
                        <th rowspan="2" scope="colgroup">No</th>
                        <th rowspan="2" scope="colgroup">Les matières</th>
                        <th colspan="{{$epeMaxLenght}}" scope="colgroup">Les interrogations</th>
                        <th colspan="{{$participMaxLenght}}" scope="colgroup">Les Participations</th>
                        <th colspan="{{$devMaxLenght}}" scope="colgroup">Les devoirs</th>
                        <th colspan="3" scope="colgroup">Les Moyennes</th>
                        <td rowspan="2">Rang</td>
                        <td rowspan="2">
                            <span class="bi-tools"></span>
                        </td>
                    </tr>
                    <tr class="text-center bg-secondary-light-2">
                        @for ($e = 1; $e <= $epeMaxLenght; $e++)
                            <th scope="col" class="epe{{$e}}">EPE {{ $e }}</th>
                        @endfor
                        @for ($p = 1; $p <= $participMaxLenght; $p++)
                            <th scope="col" class="particip{{$p}}">Part. {{ $p }}</th>
                        @endfor
                        @for ($d = 1; $d <= $devMaxLenght; $d++)
                            <th scope="col" class="epe{{$d}}">DEV {{ $d }}</th>
                        @endfor
                        <th scope="col">Moy. Int</th>
                        <th scope="col">Moy</th>
                        <th scope="col">Moy. Coef</th>
                    </tr>
                    @foreach($marks as $subject_id => $subject)
                        <tr class="text-left">
                            <th scope="row" class="text-center border-right">{{ $loop->iteration }}</th>
                            <th class="text-capitalize pl-2 p-0 m-0">
                                {{ $subject['name'] }}

                                @if($pupil->getCurrentClasse())
                                <span class="float-right text-right mr-2">
                                    <span class="mt-5">
                                        <small class="text-success">
                                            ({{ $pupil->getRelatedMarksCounter($pupil->getCurrentClasse()->id, $subject_id, session('semestre_selected'), session('school_year_selected'), 'bonus', true) }}) 
                                        </small>
                                            <small>  </small>
                                         <small class="text-danger">
                                             ({{ $pupil->getRelatedMarksCounter($pupil->getCurrentClasse()->id, $subject_id, session('semestre_selected'), session('school_year_selected'), 'minus', true) }})
                                        </small>

                                    </span>
                                </span>
                                @endif
                            </th>
                            @if($subject)
                                {{-- LES EPE --}}
                                @if($subject['epe'])
                                    @foreach ($subject['epe'] as $m => $epe)
                                    <td class="text-center cursor-pointer">
                                        <span class="w-100 cursor-pointer"> {{ numb_formatted($epe->value) }} </span>
                                    </td>
                                    @endforeach
                                    @if ($subject['epe'] && count($subject['epe']) < $epeMaxLenght)
                                        @for ($e = (count($subject['epe']) + 1); $e <= $epeMaxLenght; $e++)
                                            <td class="text-center cursor-pointer"> - </td>
                                        @endfor
                                    @endif
                                @else
                                    @for ($epev=1; $epev <= $epeMaxLenght; $epev++)
                                        <td class="text-center cursor-pointer"> - </td>
                                        </td>
                                    @endfor
                                @endif

                                {{-- LES PARTICIPATIONS --}}
                                @if($subject['participation'])
                                    @foreach ($subject['participation'] as $l => $part)
                                        <td class="text-center cursor-pointer"> {{ $part->value }} </td>
                                    @endforeach
                                    @if ($subject['participation'] && count($subject['participation']) < $participMaxLenght)
                                        @for ($part=(count($subject['participation']) + 1); $part <= $participMaxLenght; $part++)
                                            <td class="text-center cursor-pointer"> - </td>
                                        @endfor
                                    @endif
                                @else
                                    @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                        <td class="text-center cursor-pointer"> - </td>
                                    @endfor
                                @endif

                                {{-- LES DEVOIRS --}}
                                @if ($subject['devoir'])
                                    @foreach ($subject['devoir'] as $q => $dev)
                                        <td class="text-center cursor-pointer"> {{ numb_formatted($dev->value) }} </td>
                                    @endforeach
                                    @if ($subject['devoir'] && count($subject['devoir']) < $devMaxLenght)
                                        @for ($d=(count($subject['devoir']) + 1); $d <= $devMaxLenght; $d++)
                                            <td class="text-center cursor-pointer"> - </td>
                                        @endfor
                                    @endif

                                @else
                                    @for ($dvv=1; $dvv <= $devMaxLenght; $dvv++)
                                        <td class="text-center cursor-pointer"> - </td>
                                    @endfor
                                @endif
                                @php


                                @endphp
                                @if(isset($averageEPETabs[$subject_id]))
                                <td class=" text-center moy-epe-note {{$averageEPETabs[$subject_id] !== null ? ($averageEPETabs[$subject_id] >= 10 ? 'text-success' : 'text-danger') : 'text-warning'}}">
                                    {{ $averageEPETabs[$subject_id] !== null ? numb_formatted($averageEPETabs[$subject_id]) : ' - ' }} 
                                </td>
                                @else
                                <td class="text-center">
                                    <small>-</small>
                                </td>
                                @endif

                                @if(isset($averageTabs[$subject_id]))
                                <td class=" text-center moy-note {{$averageTabs[$subject_id] !== null ? ($averageTabs[$subject_id] >= 10 ? 'bg-success' : 'bg-danger') : 'bg-secondary'}}"> 
                                    
                                    {{ $averageTabs[$subject_id] !== null ? numb_formatted($averageTabs[$subject_id]) : ' - ' }}
                                </td>
                                @else
                                <td class="text-center">
                                    <small>-</small>
                                </td>
                                @endif

                                @if(isset($averageTabs[$subject_id]))
                                <td class=" text-center moy-coef-note"> 
                                    {{ $averageTabs[$subject_id] !== null ? numb_formatted($averageTabs[$subject_id] * $classeCoefTabs[$subject_id]) : ' - ' }}
                                </td>
                                @else
                                <td class="text-center">
                                    <small>-</small>
                                </td>
                                @endif

                                @if(isset($ranksTabs[$subject_id]))
                                <td class=" text-center rank-note">  
                                    @if($ranksTabs[$subject_id])
                                        <span>{{ $ranksTabs[$subject_id]['rank']}}</span><sup>{{ $ranksTabs[$subject_id]['exp']}}</sup>
                                        <small> {{ $ranksTabs[$subject_id]['base'] }} </small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small>-</small>
                                </td>
                                @endif
                                
                                
                            @else
                                @for ($ev=1; $ev <= $epeMaxLenght; $ev++)
                                    <td class="text-center cursor-pointer"> - </td>
                                @endfor
                                @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                    <td class="text-center cursor-pointer"> - </td>
                                @endfor
                                @for ($dv=1; $dv <= $devMaxLenght; $dv++)
                                    <td class="text-center cursor-pointer"> - </td>
                                @endfor
                                <td class=" text-center moy-epe-note"> - </td>
                                <td class=" text-center moy-note"> - </td>
                                <td class=" text-center moy-coef-note"> - </td>
                                <td class=" text-center rank-note"> - </td>
                                
                            @endif
                        </tr>
                    @endforeach
            </table> 
            @else
                <h6 class="text-center text-warning border border-warning p-2 w-auto px-3 bg-orange letter-spacing-12">Veuiller sélectionner un {{ $semestre_type }} pour voir les notes de {{ $pupil->getName() }}</h6>
            @endif

            <div class="m-0 p-0 w-100 mt-3 mb-2 bg-secondary-light-0">
                <hr class="text-warning w-100 m-0 p-0 bg-primary">
                <blockquote class="text-warning px-2 py-1 m-0 ">
                    <span style="letter-spacing: 1.2px" class="fx-20 font-weight-bold text-uppercase font-italic">
                        Les détails sur les moyennes par {{$semestre_type}}
                    </span>
                </blockquote>
                <hr class="text-warning w-100 m-0 p-0 bg-primary">
            </div>

            <div class="d-flex mx-auto w-100 my-2 p-3 row rounded border justify-content-between">
                @foreach($semestres as $semestre)
                    @if(is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id) || is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id, $semestre))
                        <div style="width: {{(100/(count($semestres)) - 2)}}%" class="card shadow border mx-1 my-2 p-0 bg-secondary-light-{{$semestre}}">
                            <h6 class="w-100 m-0 p-0 p-2 text-warning"> 
                                <span class="p-2 mt-1">{{$semestre_type .' '. $semestre}}</span>  
                                <span class="text-white bg-success border rounded border-white float-right letter-spacing-12 p-2 px-3 mr-2">
                                    Semestre Terminé
                                </span>
                            </h6>
                            <hr class="w-100 m-0 p-0 bg-orange">

                            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center bg-secondary-light-{{$semestre}}">
                                <colgroup span="3"></colgroup>
                                <colgroup span="3"></colgroup>

                                <tr>
                                    <th class="py-2 px-1" colspan="3">Apprenants</th>
                                    <th colspan="3">Classe</th>
                                </tr>
                                @php 
                                    if($semestrialAverages && $semestrialAverages[$semestre]){

                                        $semestrialAverage = $semestrialAverages[$semestre];

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
                                <tr>
                                    <th class="py-2 px-1" scope="col">Moyenne</th>
                                    <th class="px-1" scope="col">Rang</th>
                                    <th class="px-1" scope="col">Mention</th>
                                    <th class="px-1" scope="col">Faible moyenne</th>
                                    <th class="px-1" scope="col">Forte moyenne</th>
                                    <th class="px-1" scope="col">Effectif</th>
                                </tr>

                                <tr>
                                   <td class=" text-center py-2 px-1"> 
                                        @if($semestrialAverage)
                                            <span class="{{$moy_sm >= 10 ? 'text-green-y' : 'text-danger'}}">
                                                {{ numb_formatted($moy_sm) }}
                                            </span>

                                        @else
                                            <small class="text-white-50 font-italic">Non prêt</small>
                                        @endif
                                    </td>
                                    <td class=" text-center"> 
                                        @if($semestrialAverage)
                                            <span>{{$rank_sm}}</span><sup>{{$exp_sm}}</sup><small>{{$base_sm }} </small>
                                        @else
                                            <small class="text-white-50 font-italic">Non classé</small>
                                        @endif
                                    </td>
                                    <td class=" text-center "> 
                                        @if($semestrialAverage)
                                           <span class="{{$moy_sm >= 10 ? 'text-green-y' : 'text-danger'}}">
                                            {{ $mention_sm }}
                                        </span>
                                        @else
                                            {{ ' - ' }}
                                        @endif

                                    </td>
                                    <td class=" text-center "> 
                                        @if($semestrialAverage)
                                            <span class="{{$min_sm >= 10 ? 'text-green-y' : 'text-danger'}}">
                                                {{ numb_formatted($min_sm) }}
                                            </span>
                                        @else
                                            {{ ' - ' }}
                                        @endif
                                    </td>
                                    <td class=" text-center "> 
                                        @if($semestrialAverage)
                                            <span class="{{$max_sm >= 10 ? 'text-green-y' : 'text-danger'}}">
                                                {{ numb_formatted($max_sm) }}
                                            </span>
                                        @else
                                            {{ ' - ' }}
                                        @endif
                                    </td>
                                    <td class=" text-center "> 
                                        {{ $effectif > 9 ? $effectif : '0' . $effectif }}
                                    </td> 
                                </tr>

                            </table>


                        </div>
                    @endif
                @endforeach

                @if(is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id, []))
                    <div class=" text-orange p-2 card shadow my-2 border bg-secondary-light-0 w-100">
                        <h6 class="w-100 m-0 p-0 p-2"> 

                            <span>Moyenne Générale Annuelle de l'année-scolaire {{ $school_year_model->school_year }}</span> 

                            <span class="text-warning bg-success border rounded border-warning float-right letter-spacing-12 p-2 px-3 mr-2">
                                Année-scolaire clôturée
                            </span>

                        </h6>
                        <hr class="w-100 m-0 p-0 ">

                        <table class="w-100 m-0 p-0 table-striped table-bordered z-table bg-secondary-light-0 text-white text-center">
                            <colgroup span="3"></colgroup>
                            <colgroup span="3"></colgroup>
                            <tr>
                                <th class="py-2 px-1" colspan="5">Apprenants</th>
                                <th colspan="3">Classe</th>
                            </tr>
                            <tr>

                                @php 

                                    if($annualAverage && is_object($annualAverage)){

                                        $moy_an = $annualAverage->moy;

                                        $mention_an = $annualAverage->mention;

                                        $min_an = $annualAverage->min;

                                        $max_an = $annualAverage->max;

                                    }
                                    else{

                                        $annualAverage = null;

                                    }

                                @endphp

                                <th class="py-2 px-1" scope="col">Moyenne</th>
                                <th class="px-1" scope="col">Rang</th>
                                <th class="px-1" scope="col">Mention</th>
                                <th class="px-1" scope="col">Décision</th>
                                <th class="px-1" scope="col">Félicitations</th>
                                <th class="px-1" scope="col">Faible moyenne</th>
                                <th class="px-1" scope="col">Forte moyenne</th>
                                <th class="px-1" scope="col">Effectif</th>
                            </tr>

                            <tr class="text-cursive cursive fx-20">
                                <td class=" text-center py-2 px-1"> 
                                    @if($annualAverage !== null)
                                    <span class="{{$moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ numb_formatted($moy_an) }}
                                    </span>
                                    @else
                                        <small class="text-white-50 font-italic">Non prêt</small>
                                    @endif
                                </td>
                                <td class=" text-center"> 
                                    @if($annualAverage !== null)
                                        <span>{{$annualAverage->rank}}</span><sup>{{$annualAverage->exp}}</sup><small>{{$annualAverage->base }} </small>
                                    @else
                                        <small class="text-white-50 font-italic">Non classé</small>
                                    @endif
                                </td>
                                <td class=" text-center "> 
                                    @if($annualAverage !== null)
                                       <span class="{{ $moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ $mention_an }}
                                    </span>
                                    @else
                                        {{ ' - ' }}
                                    @endif

                                </td>
                                <td class=" text-center "> 
                                    @if($annualAverage !== null)
                                       <span class="{{ $moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ $moy_an >= 10 ? 'Admise (e)' : 'Réfusé (e)' }}
                                    </span>
                                    @else
                                        {{ ' - ' }}
                                    @endif

                                </td>
                                <td class=" text-center "> 
                                    @if($annualAverage !== null)
                                       <span class="{{ $moy_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ ' - ' }}
                                    </span>
                                    @else
                                        {{ ' - ' }}
                                    @endif

                                </td>
                                <td class=" text-center "> 
                                    @if($annualAverage !== null)
                                    <span class="{{$min_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ numb_formatted($min_an) }}
                                    </span>
                                    @else
                                        {{ ' - ' }}
                                    @endif
                                </td>
                                <td class=" text-center "> 
                                    @if($annualAverage !== null)
                                    <span class="{{$max_an >= 10 ? 'text-green-y' : 'text-danger'}}">
                                        {{ numb_formatted($max_an) }}
                                    </span>
                                    @else
                                        {{ ' - ' }}
                                    @endif
                                </td>
                                <td class=" text-center "> 
                                    {{ $effectif > 9 ? $effectif : '0' . $effectif }}
                                </td>
                            </tr>
                        </table>

                    </div>
                @endif
                <div class="mt-1 border rounded bg-secondary-light-2">

                    <h6 class="p-2 text-primary letter-spacing-12">
                        <p class="fx-25 text-uppercase text-warning p-0 m-0">A Savoir</p>
                        <hr class="m-0 p-0 w-100 my-2">
                        Chers parents; les moyennes semestrielles et générales sont générées et mentionnées une fois que le ou les semestres sont bouclés<br>
                        Celles-ci sont les moyennes définitives et ne sont pas modifiables!<br>
                        <br>
                        La moyenne définitive est celle renseignée à la fin du semestre/trimestre avec le sticker <span class="text-warning">Validée ou Terminée</span> <br>
                        La moyenne générale officielle n'est disponible qu'à la fin de tous les semestres/trimestres. 
                        <span class="text-warning float-right p-2">Nous sommes disponibles pour toutes sugestions!!!</span>


                    </h6>

                </div>  
            </div>                                                 
        </div>
        @elseif($pupil && !$classe || !$school_year_model)
        <div class="my-2 p-2 text-center border rounded">
            <h6 class="mx-auto p-3 text-warning fx-20">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                
                <h6 class="text-warning fx-20 letter-spacing-12">
                    Il parait qu'aucune donnée n'est disponible pour l'apprenant(e) <span class="text-success underline">{{ $pupil->getName() }}</span> 
                    pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span>
                </h6>

                <blockquote class="text-info fx-20 letter-spacing-12">
                    Veuillez sectionner une autre année scolaire
                </blockquote>
            </h6>
        </div>
        @endif
    </div>

</div>



</div>