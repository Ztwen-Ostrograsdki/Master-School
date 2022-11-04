<div x-data="{}">
    <div class="w-100 my-1">
        <span wire:click="editClasseSubjects({{$classe->id}})" class="btn btn-primary border border-white" title="Ajouter une matière à cette classe">
            <span class="fa fa-bookmark"></span>
            <span>Ajouter</span>
        </span>

        <select id="classe_subject_selected" wire:model="classe_subject_selected" wire:change="changeSubject" class="form-select"
        >
            <option value="{{null}}">Veuillez sélectionner une matière</option>
            @foreach ($classe_subjects as $subject)
            <option value="{{$subject->id}}">{{$subject->name}}</option>
            @endforeach
        </select>
    </div>
    <h5 class="border-top">les notes</h5>
    <div>
        @if($pupils && $classe_subject_selected && count($pupils) > 0)
        <div class="w-100 m-0 p-0 mt-3">
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                <thead class="text-white text-center">
                    <th class="py-2 text-center">#ID</th>
                    <th class="">Apprenant(e)</th>
                    <th class="">
                        <table class="w-100">
                            <thead class="text-uppercase">
                                <th colspan="{{$epeMaxLenght}}">Les interrogations</th>
                                <th class="" colspan="{{$devMaxLenght}}">Les devoirs</th>
                                <th class="" colspan="3">Les moyennes</th>
                                <th class="" colspan="">Le rang</th>
                            </thead>
                            <tbody>
                                <tr>
                                    @for ($i = 1; $i <= $epeMaxLenght; $i++)
                                        <td class="epe{{$i}}">EPE {{ $i }}</td>
                                    @endfor
                                    @for ($u = 1; $u <= $devMaxLenght; $u++)
                                        <td class="dev{{$u}}">DEV {{ $u }}</td>
                                    @endfor
                                    <td class="moy-epe">Moy. Interro</td>
                                    <td class="moy">Moy.</td>
                                    <td class="moy-coef">Moy. Coef.</td>
                                    <td class="rank">Rang</td>
                                </tr>
                            </tbody>
                        </table>
                    </th>
                    <th>Action</th>
                </thead>
                <tbody>
                    @foreach($pupils as $k => $p)
                        <tr class=" ">
                            <td class="text-center border-right">{{ $k + 1 }}</td>
                            <td class="text-capitalize pl-2">
                                <span class="d-flex">
                                    <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                    <span class="mx-2 d-none d-lg-inline d-xl-inline">
                                        {{$p->firstName}}
                                    </span>
                                    <span class="">
                                        {{ $p->lastName }}
                                    </span>
                                </span>
                            </td>
                            <td>
                                <table class="w-100">
                                    <tbody>
                                        <tr>
                                            @foreach ($marks[$p->id]['epe'] as $m => $epe)
                                            <td class="epe-note{{$m+1}}" >{{$epe->value}}</td>
                                            @endforeach

                                            @if ($marks[$p->id]['epe'] && count($marks[$p->id]['epe']) < $epeMaxLenght)
                                                @for ($e=(count($marks[$p->id]['epe']) + 1); $e <= $epeMaxLenght; $e++)
                                                    <td class="text-center epe-note{{$e}}" > - </td>
                                                @endfor
                                            @endif
                                            @foreach ($marks[$p->id]['dev'] as $q => $dev)
                                            <td  class="dev-note{{$q+1}}">{{$dev->value}}</td>
                                            @endforeach
                                            @if ($marks[$p->id]['dev'] && count($marks[$p->id]['dev']) < $devMaxLenght)
                                                @for ($d=(count($marks[$p->id]['dev']) + 1); $d <= $devMaxLenght; $d++)
                                                    <td class="text-center dev-note{{$d}}" > - </td>
                                                @endfor
                                            @endif
                                            <td class="moy-epe-note"> - </td>
                                            <td class="moy-note"> - </td>
                                            <td class="moy-coef-note"> - </td>
                                            <td class="rank-note"> - </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td>actions</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>                                                     
        </div>
        @endif
    </div>

</div>
