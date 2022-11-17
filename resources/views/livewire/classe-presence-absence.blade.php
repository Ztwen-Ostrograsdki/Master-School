<div>
    @if($classe)
    @if($makePresence)
    <blockquote class="text-info d-flex border bg-success" style="position: fixed; right: 10px; top: 200px; z-index: 3000 !important; opacity: 0.96;">
        <h5 class="w-100 m-0 p-0">
            @if($subject_selected)
                <div class="d-flex row">
                    <div class="col-2">
                        <div class="p-0 m-0 mt-0 mb-2 row col-12 px-2">
                            <input placeholder="La date" class="form-control bg-transparent border border-white px-2 @error('date') text-danger border-danger @enderror" wire:model="date" type="date" name="date" id="{{rand(158785, 859745525)}}">
                        </div>
                    </div>
                    <div class="col-1">
                        <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('coming_hour_H') text-danger border border-danger @enderror" wire:model.defer="coming_hour_H" name="coming_hour_H" id="coming_hour_H">
                            <option disabled class="" value="{{null}}">Heure d'arrivée</option>
                            @for ($ch = 7; $ch < 20; $ch++)
                                <option  value="{{$ch}}">{{$ch . 'H'}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-1">
                        <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('coming_hour_M') text-danger border border-danger @enderror" wire:model="coming_hour_M" name="coming_hour_M" id="coming_hour_M">
                            <option disabled class="" value="{{null}}">minutes d'arrivée</option>
                            @for ($cm = 0; $cm < 60; $cm++)
                                <option  value="{{$cm}}">{{$cm . 'min'}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-1">
                        <select class="px-2 form-select custom-select bg-success text-dark custom-select bg-transparent w-100 @error('duration') text-danger border border-danger @enderror" wire:model.defer="duration" name="duration" id="duration">
                            <option disabled class="" value="{{null}}">Minutes manquées</option>
                            @for ($m = 5; $m < 3600; $m++)
                                <option  value="{{$m}}">{{$m . 'min'}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-3">
                        <x-z-input :width="'col-12'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('motif')" :modelName="'motif'" :labelTitle="'Le motif'" ></x-z-input>
                    </div>
                    <div class="col-1">
                    <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('start') text-danger border border-danger @enderror" wire:model="start" name="start" id="start">
                            <option disabled class="" value="{{null}}">Choisissez l'heure de début</option>
                            @for ($s = 7; $s < 19; $s++)
                                <option  value="{{$s}}">{{$s . 'H'}}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-1">
                        <select class="px-2 form-select custom-select bg-success text-dark bg-transparent w-100 @error('end') text-danger border border-danger @enderror" wire:model.defer="end" name="end" id="end">
                            <option disabled class="" value="{{null}}">Choisissez l'heure de fin</option>
                            @for ($e = ($start + 1); $e < 20; $e++)
                                <option  value="{{$e}}">{{$e . 'H'}}</option>
                            @endfor
                        </select>
                    </div>
                    <span wire:click="cancelPresence" class="float-right cursor-pointer btn btn-primary border">Terminer la présence</span>
                </div>
            @else
            <span>
                Veuillez sélectionner une matière d'abord dans la section <span class="text-warning">Notes</span> avant de continuer
            </span>
            @endif
        </h5>
    </blockquote>
    @endif
    @if(!$makePresence && $subject_selected)
    <span class="text-white mt-2 h6">
        Gestionnaire de la présence de cette classe à l'année {{ session('school_year_selected') }} en <span class="text-warning"> {{ $subject_selected->name }}</span>
    </span>
    <span wire:click="throwPresence" class="float-right cursor-pointer btn btn-primary border mb-2">
        Faire la présence
        <span class="bi-clock"></span>
    </span>
    @endif
    <div class="w-100 m-0 p-0 mt-3">
    <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
        <thead class="text-white text-center">
            <th class="py-2 text-center">#ID</th>
            <th class="">Nom et Prénoms</th>
            @if ($makePresence)
            <th>Aujourd'hui</th>
            @endif
            <th>Absences</th>
            <th>Retards</th>
            <th>Action</th>
        </thead>
        <tbody>
            @foreach($pupils as $k => $p)
                <tr class="">
                    <td class="text-center border-right">{{ $loop->iteration }}</td>
                    <td class="text-capitalize pl-2" title="charger le profil de {{$p->getName()}}">
                        <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                            <span class="d-flex">
                                <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                <span class="mx-2 d-none d-lg-inline d-xl-inline text-small">
                                    {{$p->firstName}}
                                </span>
                                <span class="">
                                    {{ $p->lastName }}
                                </span>
                            </span>
                        </a>
                    </td>
                    @if ($makePresence)
                        @if ($p->isAbsentThisDay($date, $school_year_model->id, $semestre_selected, $subject_selected->id))
                        <td class="text-center">Absent</td>
                        @elseif (($p->wasLateThisDayFor($date, $school_year_model->id, $semestre_selected, $subject_selected->id)))
                        <td class="text-center text-warning">Retard de
                            {{ ($p->wasLateThisDayFor($date, $school_year_model->id, $semestre_selected, $subject_selected->id))->duration }} min
                        </td>
                        @else
                        <td class="text-center text-success">Présent
                            <span class="fa bi-clock-history"></span>
                        </td>
                        @endif
                    @endif
                    <td class="text-center">{{ $p->getAbsencesCounter() }}</td>
                    <td class="text-center"> {{ $p->getLatesCounter() }}</td>
                    <td class="text-center w-auto p-0">
                        <span class="row w-100 m-0 p-0">
                            @if ($makePresence)
                                @if ($p->isAbsentThisDay($date, $school_year_model->id, $semestre_selected, $subject_selected->id))
                                <span title="Annuler l'absence d'aujourd'hui de {{$p->name}}" wire:click="cancelAbsence({{$p->id}})" class="text-info col-4 m-0 p-0 cursor-pointer">
                                    <span class="fa fa-unlock py-2 px-2"></span>
                                </span>
                                @else
                                <span title="Marquer absent {{$p->name}}" wire:click="absent({{$p->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa bi-person-x py-2 px-2"></span>
                                </span>
                                @endif
                                @if ($p->wasLateThisDayFor($date, $school_year_model->id, $semestre_selected, $subject_selected->id))
                                <span title="Annuler le retard d'aujourd'hui de {{$p->name}}" wire:click="cancelLate({{$p->id}})" class="text-info col-4 m-0 p-0 border-right border-left cursor-pointer">
                                    <span class="fa bi-unlock py-2 px-2"></span>
                                </span>
                                @else
                                <span title="Marquer comme retardataire {{$p->name}}" wire:click="late({{$p->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-clock-history py-2 px-2"></span>
                                </span>
                                @endif
                                <span title="Marquer {{$p->name}}" class="text-primary col-4 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-messenger py-2 px-2"></span>
                                </span>
                            @endif

                        </span>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>                                                     
    </div>
    @endif
</div>