<div x-data="{editing_mark: @entangle('editing_mark'), edit_key: @entangle('edit_key'), mark_key: @entangle('mark_key'), olders: null}">
    <div class="w-100 my-1">
        <span wire:click="addNewPupil" class="btn btn-primary border border-white float-right" title="Ajouter une matière à cette classe">
            <span  class="fa fa-bookmark"></span>
            <span>Ajouter 1</span>
        </span>

    </div>
    <div class="my-2">
        @if($pupil)
        <div>
            <blockquote class="text-primary">
                <h5 class="m-0 p-0 text-white-50">
                    Les détails sur les notes de {{$pupil->getName()}} année-scolaire {{session('school_year_selected')}} classe {{$pupil->getCurrentClasse() ? $pupil->getCurrentClasse()->name : ''}}
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
                        <th rowspan="2" scope="colgroup">Les matières</th>
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
                    @foreach($marks as $k => $subject)
                        <tr class="text-left">
                            <th scope="row" class="text-center border-right">{{ $loop->iteration }}</th>
                            <th class="text-capitalize pl-2 p-0 m-0">
                                {{ $subject['name'] }}
                            </th>
                            @if($subject)
                                {{-- LES EPE --}}
                                @if($subject['epe'])
                                    @foreach ($subject['epe'] as $m => $epe)
                                    <td class="text-center cursor-pointer">
                                        <span class="w-100 cursor-pointer"> {{ $epe->value }} </span>
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
                                        <td class="text-center cursor-pointer"> {{ $dev->value }} </td>
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
                                <td class=" text-center moy-epe-note"> - </td>
                                <td class=" text-center moy-note"> - </td>
                                <td class=" text-center moy-coef-note"> - </td>
                                <td class=" text-center rank-note"> - </td>
                                <td class="text-center">actions</td>
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
                                <td class="text-center"> - </td>
                            @endif
                        </tr>
                    @endforeach
            </table>                                                     
        </div>
        @endif
        @if($noMarks || !$pupil)
        <div class="my-2 p-2 text-center border rounded">
            <h6 class="mx-auto p-3">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cet apprenant de 
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
