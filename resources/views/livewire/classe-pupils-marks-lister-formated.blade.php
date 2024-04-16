<div>
    <div class="w-100 my-1">
        @livewire('classe-marks-header-component', ['classe_id' => $classe->id, 'simpleFormat' => $simpleFormat])
    </div>

    @if($is_loading)
    <div class="w-100 d-flex justify-content-center flex-column">
        @livewire('loader-component')  
    </div>
    @else
    <div class="my-2">
        @if($pupils && $classe_subject_selected && count($pupils) > 0)
            <div class="w-100 m-0 p-0 mt-3">
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table hoverable text-white text-center">
                    <col>
                    <col>
                    <col>
                    <col span="{{$devMaxLenght}}">
                    <col >
                    <col span="1">
                    <col span="1">
                    <tr class="text-center">
                        <td >No</td>
                        <td class="">Matricule</td>
                        <td class="py-2" >Les apprenants</td>
                        <td >Moy. Int</td>
                        @for ($d = 1; $d <= $devMaxLenght; $d++)
                            <td  class="dev{{$d}}">DEV {{ $d }}</td>
                        @endfor
                        <td >Moy</td>
                        <td >Moy. Coef</td>
                        @if(!$simpleFormat)
                        <td >Rang</td>
                        @endif
                        <td >
                            <span class="bi-tools"></span>
                        </td>
                    </tr>
                    
                        @foreach($pupils as $k => $p)
                            @if(!$p->abandonned)
                                <tr class="text-left">
                                    <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                                    <th scope="row" class="text-left letter-spacing-12 pl-2">{{ $p->ltpk_matricule }}</th>
                                    <th class="text-capitalize p-0 m-0 d-flex justify-content-between">
                                        @isMaster(auth()->user())
                                        <a class="text-white m-0 p-0 py-1" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                            <span class="">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                                    {{$p->getName()}}
                                                </span>
                                            </span>
                                        </a>
                                        @else
                                        <span class="text-white m-0 p-0 py-1">
                                            <span class="">
                                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                                    {{$p->getName()}}
                                                </span>
                                            </span>
                                        </span>
                                        @endisMaster
                                        <span class="float-right text-right">
                                            <small class="mx-1">
                                                <small class="text-success">
                                                    ({{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'bonus', true) }}) 
                                                </small>
                                                 <small class="text-danger">
                                                     ({{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'minus', true) }})
                                                </small>
                                            </small>
                                        </span>
                                    </th>
                                    @if(isset($marks[$p->id]) && $marks[$p->id])
                                    <td class=" text-center moy-epe-note {{$averageEPETab[$p->id] !== null ? ($averageEPETab[$p->id] >= 10 ? 'text-success' : 'text-danger') : 'text-warning' }} "> 
                                            @if($averageEPETab)
                                                {{ $averageEPETab[$p->id] !== null ? ($averageEPETab[$p->id] >= 10 ? $averageEPETab[$p->id] : '0'.$averageEPETab[$p->id]) : ' - ' }} 
                                            @else
                                                {{ ' - ' }}
                                            @endif
                                        </td>
                                        @if ($marks[$p->id]['dev'])
                                            @foreach ($marks[$p->id]['dev'] as $q => $dev)
                                                <td title="Devoir No {{$dev->mark_index}} éditée le {{ ucwords($dev->__getDateAsString($dev->updated_at, null, true)) }} " wire:click="setTargetedMark({{$dev->id}}, 'devoir')" class="text-center cursor-pointer">
                                                    <span class="w-100 cursor-pointer @if($dev && $dev->value && $dev->value < 10) text-danger  @else text-primary @endif "> {{ $dev->value >= 10 ? $dev->value : '0'.$dev->value }} </span>
                                                </td>
                                            @endforeach
                                            @if ($marks[$p->id]['dev'] && count($marks[$p->id]['dev']) < $devMaxLenght)
                                                @for ($d=(count($marks[$p->id]['dev']) + 1); $d <= $devMaxLenght; $d++)
                                                    <td wire:click="insertMarks({{$p->id}}, 'devoir')" class="text-center cursor-pointer">
                                                        <span  class="w-100 cursor-pointer"> - </span>
                                                    </td>
                                                @endfor
                                            @endif

                                        @else
                                            @for ($dvv=1; $dvv <= $devMaxLenght; $dvv++)
                                                <td  wire:click="insertMarks({{$p->id}}, 'devoir')" class="text-center cursor-pointer">
                                                    <span  class="w-100 cursor-pointer"> - </span>
                                                </td>
                                            @endfor
                                        @endif
                                        <td class=" text-center moy-note text-white-50 z-hover {{ $averageTab[$p->id] !== null ? ($averageTab[$p->id] >= 10 ? 'bg-success' : 'bg-danger') : 'bg-secondary' }} "> 
                                            @if($averageTab)
                                                {{ $averageTab[$p->id] !== null ? ($averageTab[$p->id] >= 10 ? $averageTab[$p->id] : '0'.$averageTab[$p->id]) : ' - ' }} 
                                            @else
                                               {{ ' - ' }}
                                            @endif
                                        </td>
                                        <td class=" text-center moy-coef-note">
                                            @if($averageTab) 
                                                {{ $averageTab[$p->id] !== null ? ($averageTab[$p->id] * $classe_subject_coef >= 10 ? ($averageTab[$p->id] * $classe_subject_coef) : '0'.($averageTab[$p->id] * $classe_subject_coef)) : ' - ' }}
                                            @else
                                                {{ ' - ' }}
                                            @endif
                                        </td>
                                        @if(!$simpleFormat)
                                            <td class=" text-center rank-note text-warning"> 
                                                @if($ranksTab)
                                                    @isset($ranksTab[$p->id])
                                                        <span>{{ $ranksTab[$p->id]['rank']}}</span><sup>{{ $ranksTab[$p->id]['exp']}}</sup>
                                                        <small> {{ $ranksTab[$p->id]['base'] }} </small>
                                                    @else
                                                        {{ ' - ' }}
                                                    @endif
                                                @else
                                                    {{ ' - ' }}
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-center">
                                            <span wire:click="insertRelatedMark({{$p->id}})" title="Ajouter une note relative : Sanction ou Bonus" class="cursor-pointer">
                                                <strong class="bi-plus text-success"></strong>/
                                                <strong class="text-danger"> - </strong>
                                            </span>
                                        </td>
                                    @else
                                        <td class=" text-center moy-epe-note"> </td>
                                        @for ($dv=1; $dv <= $devMaxLenght; $dv++)
                                            <td class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                        <td class=" text-center moy-note"> - </td>
                                        <td class=" text-center moy-coef-note"> - </td>
                                        @if(!$simpleFormat)
                                            <td class=" text-center rank-note"> - </td>
                                        @endif
                                        <td class="text-center"> - </td>
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    </table> 
                <div class="d-flex justify-content-end w-100 my-1 p-2">
                    <span wire:click="printSingleMarksAsExcelFile" class="btn btn-success p-2 px-4 z-scale" title="Imprimer les notes de classe de la classe {{$classe->name}}">
                        <span class=" fa fa-print"></span>
                        <span>Telecharger en fichier Excel</span>
                    </span>
                </div>                                                    
            </div>
        @else
            <div>
                <div class="d-flex justify-content-center mx-auto mt-4  w-100">
                    <span class="fa fa-trash text-muted fa-8x"></span>
                </div>
                <div class="d-flex justify-content-center mx-auto mt-3 w-100">
                    <h4 class="letter-spacing-12 font-italic text-orange">OUUUPPPS, aucune note n'a été trouvé!!!</h4>
                </div>
                <blockquote class="text-warning">
                    <span class="float-right border-top border-white w-100 d-inline-block text-right">
                        <i class="text-warning small">OUPPPS pas de notes!!!!!</i>
                    </span>
                </blockquote>
            </div>
        @endif
        @if($noMarks || !$pupils)
        <div class="my-2 p-2 text-center border rounded text-white-50">
            <h6 class="mx-auto p-3 text-white-50">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cette classe de 
                <span class="text-warning">{{ $classe ? $classe->name : 'inconnue' }}</span> 
                pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                pour le <span class="text-warning">{{ $semestre_type . ' ' . session('semestre_selected')}}</span>

                <blockquote class="text-info">
                    Veuillez sectionner un autre {{ $semestre_type }} ou une autre année scolaire
                </blockquote>
            </h6>
        </div>
        @endif
    </div>
    @endif

</div>
