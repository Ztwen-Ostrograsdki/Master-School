<div x-data="{editing_mark: @entangle('editing_mark'), edit_key: @entangle('edit_key'), mark_key: @entangle('mark_key'), olders: null};">
    <div class="w-100 my-1">
        <select id="classe_subject_selected" wire:model="classe_subject_selected" wire:change="changeSubject" class="form-select">
            <option value="{{null}}">Veuillez sélectionner une matière</option>
            @foreach ($classe_subjects as $subject)
            <option value="{{$subject->id}}">{{$subject->name}}</option>
            @endforeach
        </select>

        <small class="text-warning m-2">
            @if($modality)
                <small class="bi-calculator mr-1"></small>Pour le calcule des moyennes d'interros de {{$subject_selected->name}}, <b class="text-success">0{{$modality}}</b> notes seront prises en comptes!
            @else
                <small class="bi-calculator mr-1"></small>Pour le calcule des moyennes d'interros de {{$subject_selected->name}}, toutes les notes seront prises en comptes!
            @endif
        </small>
        @if($hasModalities)
        <span class="text-warning float-right btn btn-secondary border">
            @if($modalitiesActivated)
                <span wire:click="diseableModalities" title="Désactiver tamporairement les modalités" class="bi-lock text-warning d-inline-block w-100 cursor-pointer"></span>
            @else
               <span wire:click="activateModalities" title="Réactiver les modalités" class="bi-unlock text-success d-inline-block w-100 cursor-pointer"></span>
            @endif
        </span>
        @endif
        <span wire:click="manageModality" class="btn btn-primary border border-white float-right mx-1" title="Editer les modalités de calcule de moyenne dans la matière sélectionnée dans cette classe">
            <span class="fa bi-pen"></span>
            <span class="fa bi-calculator"></span>
            <span>Editer</span>
        </span>
        <span wire:click="editClasseSubjects({{$classe->id}})" class="btn btn-success border border-white float-right" title="Ajouter une matière à cette classe">
            <span class="fa fa-bookmark"></span>
            <span>Ajouter</span>
        </span>

    </div>
    <div class="my-2">
        @if($pupils && $classe_subject_selected && count($pupils) > 0)
        <div>
            <blockquote class="text-primary">
                <h5 class="m-0 p-0 text-white-50">
                    Les détails sur les notes
                </h5>
            </blockquote>
        </div>
        <div class="w-100 m-0 p-0 mt-3">
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white text-center">
                    <col>
                    <col>
                    <colgroup span="{{$epeMaxLenght}}"></colgroup>
                    <colgroup span="{{$participMaxLenght}}"></colgroup>
                    <colgroup span="{{$devMaxLenght}}"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="1"></colgroup>
                    <colgroup span="1"></colgroup>
                    <tr class="text-center">
                        <td rowspan="2">No</td>
                        <td rowspan="2">Les apprenants</td>
                        <th colspan="{{$epeMaxLenght}}" scope="colgroup">Les interrogations</th>
                        <th colspan="{{$participMaxLenght}}" scope="colgroup">Les Participations</th>
                        <th colspan="{{$devMaxLenght}}" scope="colgroup">Les devoirs</th>
                        <th colspan="3" scope="colgroup">Les Moyennes</th>
                        <td rowspan="2">Rang</td>
                        <td rowspan="2">
                            <span class="bi-tools"></span>
                        </td>
                    </tr>
                    <tr class="text-center">
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
                    @foreach($pupils as $k => $p)
                        <tr class="text-left">
                            <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                            <th class="text-capitalize p-0 m-0 row justify-between">
                                <a class="text-white m-0 p-0 py-1" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="d-flex justify-content-between">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                                <span class="float-right text-right">
                                    <span class="mt-5">
                                        <small class="text-success">
                                            ({{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'bonus', true) }}) 
                                        </small>
                                            <small>  </small>
                                         <small class="text-danger">
                                             ({{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'minus', true) }})
                                        </small>

                                    </span>
                                </span>
                                <span wire:click="insertMarks({{$p->id}})" class="float-right col-1 cursor-pointer text-white-50 py-1 fa fa-edit" title="Insérer des notes"></span>
                            </th>
                            @if($marks[$p->id])
                                {{-- LES EPE --}}
                                @if($marks[$p->id]['epe'])
                                    @foreach ($marks[$p->id]['epe'] as $m => $epe)
                                        <td x-on:dblclick="@this.call('setTargetedMark', {{$p->id}}, {{$epe->id}})" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> {{ $epe->value >= 10 ? $epe->value : '0'.$epe->value}} </span>
                                        </td>
                                    @endforeach
                                    @if ($marks[$p->id]['epe'] && count($marks[$p->id]['epe']) < $epeMaxLenght)
                                        @for ($e = (count($marks[$p->id]['epe']) + 1); $e <= $epeMaxLenght; $e++)
                                            <td class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif
                                @else
                                    @for ($epev=1; $epev <= $epeMaxLenght; $epev++)
                                        <td class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                @endif

                                {{-- LES PARTICIPATIONS --}}
                                @if($marks[$p->id]['participation'])
                                    @foreach ($marks[$p->id]['participation'] as $l => $part)
                                        <td x-on:dblclick="@this.call('setTargetedMark', {{$p->id}}, {{$part->id}})" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> {{ $part->value >= 10 ? $part->value : '0'.$part->value }} </span>
                                        </td>
                                    @endforeach
                                    @if ($marks[$p->id]['participation'] && count($marks[$p->id]['participation']) < $participMaxLenght)
                                        @for ($part=(count($marks[$p->id]['participation']) + 1); $part <= $participMaxLenght; $part++)
                                            <td class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif
                                @else
                                    @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                        <td class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                @endif

                                {{-- LES DEVOIRS --}}
                                @if ($marks[$p->id]['dev'])
                                    @foreach ($marks[$p->id]['dev'] as $q => $dev)
                                        <td x-on:dblclick="@this.call('setTargetedMark', {{$p->id}}, {{$dev->id}})" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> {{ $dev->value >= 10 ? $dev->value : '0'.$dev->value }} </span>
                                        </td>
                                    @endforeach
                                    @if ($marks[$p->id]['dev'] && count($marks[$p->id]['dev']) < $devMaxLenght)
                                        @for ($d=(count($marks[$p->id]['dev']) + 1); $d <= $devMaxLenght; $d++)
                                            <td class="text-center cursor-pointer">
                                                <span  class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif

                                @else
                                    @for ($dvv=1; $dvv <= $devMaxLenght; $dvv++)
                                        <td class="text-center cursor-pointer">
                                        <span  class="w-100 cursor-pointer"> - </span>
                                    </td>
                                    @endfor
                                @endif
                                <td class=" text-center moy-epe-note {{$averageEPETab[$p->id] >= 10 ? 'text-success' : 'text-danger'}} "> {{ $averageEPETab[$p->id] >= 10 ? $averageEPETab[$p->id] : '0'.$averageEPETab[$p->id] }} </td>
                                <td class=" text-center moy-note"> - </td>
                                <td class=" text-center moy-coef-note"> - </td>
                                <td class=" text-center rank-note"> - </td>
                                <td class="text-center">
                                    <span wire:click="insertRelatedMark({{$p->id}})" title="Ajouter une note relative : Sanction ou Bonus" class="cursor-pointer">
                                        <strong class="bi-plus text-success"></strong>/
                                        <strong class="text-danger"> - </strong>
                                    </span>
                                </td>
                            @else
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
                                @for ($dv=1; $dv <= $devMaxLenght; $dv++)
                                    <td class="text-center cursor-pointer">
                                        <span class="w-100 cursor-pointer"> - </span>
                                    </td>
                                @endfor
                                <td class=" text-center moy-epe-note"> </td>
                                <td class=" text-center moy-note"> - </td>
                                <td class=" text-center moy-coef-note"> - </td>
                                <td class=" text-center rank-note"> - </td>
                                <td class="text-center"> - </td>
                            @endif
                        </tr>
                    @endforeach
                </table>                                                     
            </div>
        @endif
        @if($noMarks || !$pupils)
        <div class="my-2 p-2 text-center border rounded">
            <h6 class="mx-auto p-3">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cette classe de 
                <span class="text-warning">{{ session('classe_selected') }}</span> 
                pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                pour le <span class="text-warning">{{ $semestre_type . ' ' . session('semestre_selected')}}</span>

                <blockquote class="text-info">
                    Veuillez sectionner un autre {{ $semestre_type }} ou une autre année scolaire
                </blockquote>
            </h6>
        </div>
        @endif
    </div>

</div>
