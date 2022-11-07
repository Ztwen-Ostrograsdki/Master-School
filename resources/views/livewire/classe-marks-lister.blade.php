<div x-data="{editing_mark: @entangle('editing_mark'), edit_key: @entangle('edit_key'), mark_key: @entangle('mark_key'), olders: null};">
    <div class="w-100 my-1">
        <select id="classe_subject_selected" wire:model="classe_subject_selected" wire:change="changeSubject" class="form-select">
            <option value="{{null}}">Veuillez sélectionner une matière</option>
            @foreach ($classe_subjects as $subject)
            <option value="{{$subject->id}}">{{$subject->name}}</option>
            @endforeach
        </select>
        <span wire:click="editClasseSubjects({{$classe->id}})" class="btn btn-primary border border-white float-right" title="Ajouter une matière à cette classe">
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
                    <colgroup span="1"></colgroup>
                    <colgroup span="1"></colgroup>
                    <colgroup span="{{$epeMaxLenght}}"></colgroup>
                    <colgroup span="{{$participMaxLenght}}"></colgroup>
                    <colgroup span="{{$devMaxLenght}}"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="1"></colgroup>
                    <colgroup span="1"></colgroup>
                    <tr class="text-center">
                        <th rowspan="2" scope="colgroup">No</th>
                        <th rowspan="2" scope="colgroup">Les apprenants</th>
                        <th colspan="{{$epeMaxLenght}}" scope="colgroup">Les interrogations</th>
                        <th colspan="{{$participMaxLenght}}" scope="colgroup">Les Participations</th>
                        <th colspan="{{$devMaxLenght}}" scope="colgroup">Les devoirs</th>
                        <th colspan="3" scope="colgroup">Les Moyennes</th>
                        <th colspan="1" scope="colgroup">Le rang</th>
                        <th colspan="1" scope="colgroup">Action</th>
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
                        <th scope="col">Rang</th>
                        <th scope="col"></th>
                        
                    </tr>
                    @foreach($pupils as $k => $p)
                        <tr class="text-left">
                            <th scope="row" class="text-center border-right">{{ $k + 1 }}</th>
                            <th class="text-capitalize p-0 m-0 row">
                                <a class="text-white col-10 m-0 p-0 py-1" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="d-flex justify-content-between">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                                <span wire:click="insertMarks({{$p->id}})" class="float-right col-1 cursor-pointer py-1 fa fa-edit" title="Insérer des notes"></span>
                            </th>
                            @if($marks[$p->id])
                                {{-- LES EPE --}}
                                @if($marks[$p->id]['epe'])
                                    @foreach ($marks[$p->id]['epe'] as $m => $epe)
                                        <td x-on:dblclick="@this.call('setTargetedMark', {{$p->id}}, {{$epe->id}})" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> {{ $epe->value }} </span>
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
                                            <span class="w-100 cursor-pointer"> {{ $part->value }} </span>
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
                                            <span class="w-100 cursor-pointer"> {{$dev->value}} </span>
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
                                <td class=" text-center moy-epe-note"> - </td>
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
                                <td class=" text-center moy-epe-note"> - </td>
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
