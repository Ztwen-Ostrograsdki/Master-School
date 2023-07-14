<div>
    <div class="w-100 m-0 p-0 mt-3 px-1">

        @if($classesToShow && count($classesToShow) > 0)
            <div class="w-100 m-0 p-0 mt-3 px-1 py-2" style="overflow-x: auto;">
                <table class="m-0 p-0 w-100 table-striped table-bordered z-table text-white text-center" style="">
                    <col>
                    @foreach($classesToShow as $cl0)
                        <colgroup span="{{5}}"></colgroup>
                    @endforeach
                    <colgroup span="3"></colgroup>
                    <tr class="text-center z-bg-secondary">
                        <th rowspan="2" scope="colgroup">Les classes</th>
                        @foreach($classesToShow as $cl1)
                        <th colspan="{{5}}" scope="colgroup">
                            @isset($cl1->name)
                                {{$cl1->name}}
                                @if(auth()->user()->isAdminAs('master'))
                                    @if($cl1->classeHasTimePlans())
                                        <span wire:click="deleteClasseTimePlans({{$cl1->id}})" class="fa fa-trash text-danger cursor-pointer fx-15 m-1 float-right" title="Supprimer les Emplois du temps de la {{$cl1->name}}..."></span>
                                    @endif
                                @endif
                            @endisset
                        </th>
                        @endforeach
                    </tr>
                    @foreach($classesToShow as $cl2)
                        <th scope="col" class="z-bg-secondary-dark">L</th>
                        <th scope="col" class="z-bg-secondary-dark">M</th>
                        <th scope="col" class="z-bg-secondary-dark">M</th>
                        <th scope="col" class="z-bg-secondary-dark">J</th>
                        <th scope="col" class="z-bg-secondary-dark">V</th>
                    @endforeach
                    @foreach($morning_times1 as $hm1)
                        @php
                            $s1 = $hm1['s'];
                            $e1 = $hm1['e'];
                        @endphp
                        <tr class="text-left">
                            <th class=" text-capitalize pl-2 p-0 m-0 z-bg-secondary-light-opac text-dark">
                                {{ $s1 . 'H - ' . $e1 . 'H' }}
                            </th>


                                {{-- LES Programmes MARTINEE--}}
                            @foreach($classesToShow as $cl3)
                                @isset($cl3->name)

                                    <td wire:click="manageTime({{$cl3->id}}, '{{$s1}}', '{{$e1}}', '{{'Lundi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl3->getTimePlanSubject('Lundi', $s1, $e1, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl3->id}}, '{{$s1}}', '{{$e1}}', '{{'Mardi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl3->getTimePlanSubject('Mardi', $s1, $e1, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl3->id}}, '{{$s1}}', '{{$e1}}', '{{'Mercredi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl3->getTimePlanSubject('Mercredi', $s1, $e1, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl3->id}}, '{{$s1}}', '{{$e1}}', '{{'Jeudi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl3->getTimePlanSubject('Jeudi', $s1, $e1, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl3->id}}, '{{$s1}}', '{{$e1}}', '{{'Vendredi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl3->getTimePlanSubject('Vendredi', $s1, $e1, $subject_id) }} 
                                        </small>
                                    </td>
                                @endisset
                            @endforeach
                        </tr>
                    @endforeach

                    <tr>
                        <td class="py-1" colspan="{{count($classesToShow) * 5 + 1}}">
                            <span class="text-center text-warning py-1 d-flex justify-content-around">
                                <span>RECREATION</span>
                                <span>RECREATION</span>
                                <span>RECREATION</span>
                                <span>RECREATION</span>
                                <span>RECREATION</span>
                                <span>RECREATION</span>
                                <span>RECREATION</span>
                            </span>
                        </td>
                    </tr>
                    @foreach($morning_times2 as $hm2)
                        @php
                            $s2 = $hm2['s'];
                            $e2 = $hm2['e'];
                        @endphp
                        <tr class="text-left">
                            <th class=" text-capitalize pl-2 p-0 m-0 z-bg-secondary-light-opac text-dark">
                                {{ $s2 . 'H - ' . $e2 . 'H' }}
                            </th>


                                {{-- LES Programmes APRES RECRE--}}
                            @foreach($classesToShow as $cl4)
                                @isset($cl4->name)
                                    <td wire:click="manageTime({{$cl4->id}}, '{{$s2}}', '{{$e2}}', '{{'Lundi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl4->getTimePlanSubject('Lundi', $s2, $e2, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td  wire:click="manageTime({{$cl4->id}}, '{{$s2}}', '{{$e2}}', '{{'Mardi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer">
                                            {{ $cl4->getTimePlanSubject('Mardi', $s2, $e2, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td  wire:click="manageTime({{$cl4->id}}, '{{$s2}}', '{{$e2}}', '{{'Mercredi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl4->getTimePlanSubject('Mercredi', $s2, $e2, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td  wire:click="manageTime({{$cl4->id}}, '{{$s2}}', '{{$e2}}', '{{'Jeudi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl4->getTimePlanSubject('Jeudi', $s2, $e2, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td  wire:click="manageTime({{$cl4->id}}, '{{$s2}}', '{{$e2}}', '{{'Vendredi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl4->getTimePlanSubject('Vendredi', $s2, $e2, $subject_id) }} 
                                        </small>
                                    </td>

                                @endisset
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <td class="py-1" colspan="{{count($classesToShow) * 5 + 1}}">
                            <span class="text-center text-orange py-1 d-flex justify-content-around">
                                <span>PAUSE - APRES-MIDI</span>
                                <span>PAUSE - APRES-MIDI</span>
                                <span>PAUSE - APRES-MIDI</span>
                                <span>PAUSE - APRES-MIDI</span>
                            </span>
                        </td>
                    </tr>
                    @foreach($afternoon_times as $aft)
                        @php
                            $s3 = $aft['s'];
                            $e3 = $aft['e'];
                        @endphp
                        <tr class="text-left">
                            <th class=" text-capitalize pl-2 p-0 m-0 z-bg-secondary-light-opac text-dark">
                                {{ $s3 . 'H - ' . $e3 . 'H' }}
                            </th>


                                {{-- LES Programmes SOIREE--}}
                            @foreach($classesToShow as $cl5)
                                @isset($cl5->name)
                                    <td  wire:click="manageTime({{$cl5->id}}, '{{$s3}}', '{{$e3}}', '{{'Lundi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl5->getTimePlanSubject('Lundi', $s3, $e3, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl5->id}}, '{{$s3}}', '{{$e3}}', '{{'Mardi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl5->getTimePlanSubject('Mardi', $s3, $e3, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl5->id}}, '{{$s3}}', '{{$e3}}', '{{'Mercredi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl5->getTimePlanSubject('Mercredi', $s3, $e3, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl5->id}}, '{{$s3}}', '{{$e3}}', '{{'Jeudi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl5->getTimePlanSubject('Jeudi', $s3, $e3, $subject_id) }} 
                                        </small>
                                    </td>

                                    <td wire:click="manageTime({{$cl5->id}}, '{{$s3}}', '{{$e3}}', '{{'Vendredi'}}')" class="text-center cursor-pointer">
                                        <small class="w-100 cursor-pointer"> 
                                            {{ $cl5->getTimePlanSubject('Vendredi', $s3, $e3, $subject_id) }} 
                                        </small>
                                    </td>
                                    
                                @endisset
                            @endforeach
                        </tr>
                    @endforeach
                </table>
            @else
                <div>
                    <div class="d-flex justify-content-center mx-auto w-100">
                        <span class="fa fa-trash text-muted fa-8x"></span>
                    </div>
                    <blockquote class="text-warning">
                        <span class="float-right border-top border-white w-100 d-inline-block text-right">
                            <span class="fa fa-heart text-danger"></span>
                            <span class="fa fa-heart text-danger"></span>
                            <span class="fa fa-heart text-danger"></span>
                            <i class="text-warning small">Aucune classe n'a trouvé! Aucune donnée n'est peut-être disponible</i>
                        </span>
                    </blockquote>
                </div>
            </div>
        @endif                                                                                 
    </div>
</div>
