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
                    @if(!$simpleFormat)
                    <colgroup span="{{$epeMaxLenght}}"></colgroup>
                    <colgroup span="{{$participMaxLenght}}"></colgroup>
                    @endif
                    <colgroup span="{{$devMaxLenght}}"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="1"></colgroup>
                    <colgroup span="1"></colgroup>
                    <tr class="text-center">
                        <td rowspan="2">No</td>
                        <td rowspan="2">Les apprenants</td>
                        @if(!$simpleFormat)
                        <th colspan="{{$epeMaxLenght}}" scope="colgroup">Les interrogations</th>
                        <th colspan="{{$participMaxLenght}}" scope="colgroup">Les Participations</th>
                        @endif
                        <th colspan="{{$devMaxLenght}}" scope="colgroup">Les devoirs</th>
                        <th colspan="3" scope="colgroup">Les Moyennes</th>
                        @if(!$simpleFormat)
                        <td rowspan="2">Rang</td>
                        @endif
                        <td rowspan="2">
                            <span class="bi-tools"></span>
                        </td>
                    </tr>
                    <tr class="text-center">
                        @if(!$simpleFormat)
                            @for ($e = 1; $e <= $epeMaxLenght; $e++)
                                <th scope="col" class="epe{{$e}}">EPE {{ $e }}</th>
                            @endfor
                            @for ($p = 1; $p <= $participMaxLenght; $p++)
                                <th scope="col" class="particip{{$p}}">Part. {{ $p }}</th>
                            @endfor
                        @endif
                        @for ($d = 1; $d <= $devMaxLenght; $d++)
                            <th scope="col" class="dev{{$d}}">DEV {{ $d }}</th>
                        @endfor
                        <th scope="col">Moy. Int</th>
                        <th scope="col">Moy</th>
                        <th scope="col">Moy. Coef</th>
                        
                    </tr>
                    @foreach($pupils as $k => $p)
                        <tr class="text-left">
                            <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                            <th class="text-capitalize p-0 m-0 d-flex justify-content-between">
                                @isMaster(auth()->user())
                                <a class="text-white m-0 p-0 py-1" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                        <small title="Matricule EducMaster de {{$p->getName()}}" class="text-warning letter-spacing-12 ml-1">
                                            <b class="ml-1">EM: </b>{{ $p->ltpk_matricule }}
                                        </small>
                                    </span>
                                </a>
                                @else
                                <span class="text-white m-0 p-0 py-1">
                                    <span class="">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                        <small title="Matricule EducMaster de {{$p->getName()}}" class="text-warning letter-spacing-12 ml-1">
                                            <b class="ml-1">EM: </b>{{ $p->ltpk_matricule }}
                                        </small>
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
                                @if(!$simpleFormat)
                                    {{-- LES EPE --}}
                                    @if($marks[$p->id]['epe'])
                                        @foreach ($marks[$p->id]['epe'] as $m => $epe)
                                            <td title="interrogation No {{$epe->mark_index}} éditée le {{ ucwords($epe->__getDateAsString($epe->updated_at, null, true)) }} " wire:click="setTargetedMark({{$epe->id}})" class="@if(in_array($epe->id, $p->getChoosenMarks($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')))) bg-choosen-marks @endif text-center cursor-pointer @if($epe->value == 0) text-warning @endif ">
                                                <span class="w-100 cursor-pointer @if($epe->forget) text-cyan @endif  @if($epe->forced_mark) text-orange @endif" @if($epe->forced_mark) title="Cette note est obligatoire elle sera prise en compte dans le calcule de moyenne qu'elle soit meilleure note ou non" @endif @if($epe->forget) title="Cette note ne sera pas prise en compte pour le calcule de moyenne qu'elle soit meilleure note ou non" @endif > {{ $epe->value >= 10 ? $epe->value : '0'.$epe->value}}
                                                </span>
                                            </td>
                                        @endforeach
                                        @if ($marks[$p->id]['epe'] && count($marks[$p->id]['epe']) < $epeMaxLenght)
                                            @for ($e = (count($marks[$p->id]['epe']) + 1); $e <= $epeMaxLenght; $e++)
                                                <td wire:click="insertMarks({{$p->id}})" class="text-center cursor-pointer">
                                                    <span class="w-100 cursor-pointer"> - </span>
                                                </td>
                                            @endfor
                                        @endif
                                    @else
                                        @for ($epev=1; $epev <= $epeMaxLenght; $epev++)
                                            <td wire:click="insertMarks({{$p->id}})" class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif

                                    {{-- LES PARTICIPATIONS --}}
                                    @if($marks[$p->id]['participation'])
                                        @foreach ($marks[$p->id]['participation'] as $l => $part)
                                            <td wire:click="setTargetedMark({{$part->id}})" title="Note de Participation No {{$part->mark_index}} éditée le {{ ucwords($part->__getDateAsString($part->updated_at, null, true)) }} " wire:click="setTargetedMark({{$part->id}})" class="bg-choosen-marks text-center cursive cursor-pointer @if($part->value == 0) text-warning @endif ">
                                                <span class="w-100 cursor-pointer cursive @if($part->forget) text-cyan @endif  @if($part->forced_mark) text-orange @endif" @if($part->forced_mark) title="Cette note est obligatoire elle sera prise en compte dans le calcule de moyenne qu'elle soit meilleure note ou non" @endif @if($part->forget) title="Cette note ne sera pas prise en compte pour le calcule de moyenne qu'elle soit meilleure note ou non" @endif > {{ $part->value >= 10 ? $part->value : '0'.$part->value}} </span>
                                            </td>
                                        @endforeach
                                        @if ($marks[$p->id]['participation'] && count($marks[$p->id]['participation']) < $participMaxLenght)
                                            @for ($part=(count($marks[$p->id]['participation']) + 1); $part <= $participMaxLenght; $part++)
                                                <td wire:click="insertMarks({{$p->id}}, 'participation')" class="text-center cursor-pointer">
                                                    <span class="w-100 cursor-pointer"> - </span>
                                                </td>
                                            @endfor
                                        @endif
                                    @else
                                        @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                            <td wire:click="insertMarks({{$p->id}}, 'participation')" class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif
                                @endif

                                {{-- LES DEVOIRS --}}
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
                                <td class=" text-center moy-epe-note {{$averageEPETab[$p->id] !== null ? ($averageEPETab[$p->id] >= 10 ? 'text-success' : 'text-danger') : 'text-warning' }} "> 
                                    @if($averageEPETab)
                                        {{ $averageEPETab[$p->id] !== null ? ($averageEPETab[$p->id] >= 10 ? $averageEPETab[$p->id] : '0'.$averageEPETab[$p->id]) : ' - ' }} 
                                    @else
                                        {{ ' - ' }}
                                    @endif
                                </td>
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
                                @if(!$simpleFormat)
                                    @for ($ev=1; $ev <= $epeMaxLenght; $ev++)
                                        <td class="text-center cursor-pointer">
                                            <span  class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                    @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                        <td class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                @endif
                                @for ($dv=1; $dv <= $devMaxLenght; $dv++)
                                    <td class="text-center cursor-pointer">
                                        <span class="w-100 cursor-pointer"> - </span>
                                    </td>
                                @endfor
                                <td class=" text-center moy-epe-note"> </td>
                                <td class=" text-center moy-note"> - </td>
                                <td class=" text-center moy-coef-note"> - </td>
                                @if(!$simpleFormat)
                                    <td class=" text-center rank-note"> - </td>
                                @endif
                                <td class="text-center"> - </td>
                            @endif
                        </tr>
                        @endforeach
                    </table> 
                <div class="d-flex justify-content-end w-100 my-1 p-2">
                    <span wire:click="exportToExcelFormat" class="btn btn-success p-2 px-4 z-scale" title="Imprimer les notes de classe de la classe {{$classe->name}}">
                        <span class=" fa fa-print"></span>
                        <span>Imprimer les notes en format Excel</span>
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
