<div>
<div class="w-100 p-0" x-data>
    <blockquote class="text-info small">
        <h6 class="d-flex justify-content-between my-0">
            <span class="text-white-50 text-uppercase mt-2">
                CALENDRIER DES {{ $semestre_type }}s de l'année scolaire {{ $school_year_model->school_year }}
            </span>

            <span>
                {{csrf_field()}}
                <select x-model="$wire.school_calendar" x-on:change=" @this.call('changeSchoolCalendar');" class="form-select z-bg-secondary custom-select">
                    <option value="">Sélectionner un calendrier à afficher </option>
                    <template x-for="(l_ev, val) in $wire.
                    local_events">
                        <option :selected="val == $wire.school_calendar" x-bind:value="val" x-text="l_ev"></option>
                    </template>
                </select>
            </span>
        </h6>
    </blockquote>
</div>
<div class="m-1 p-2 w-100">
    <div class="w-100 d-flex justify-content-between">
        <span class="">
            <span wire:click="addNewEventPeriod" class="btn  btn-secondary">
                <span class="fa fa-plus"></span>
                <span class="">Un évènement</span>
            </span>
            <span wire:click="resetAllSemestreCalendars" class="btn btn-warning cursor-pointer mx-1">
                <span class="fa fa-trash"></span>
                <span>Rafraichir</span>
            </span>

            <span wire:click="definedSemestrePeriod" class="btn btn-success cursor-pointer">
                <span class="fa fa-plus"></span>
                <span>Un nouvel calendrier</span>
            </span>
        </span>

        @if($current_period)
            <span>
                <small class="text-primary">Nous sommes dans le {{ $current_period['target'] }}</small>
                <small class="text-danger">Il y a déjà {{ $current_period['passed'] }} qui se sont écoulés</small>
                <small class="text-success">Il nous reste encore {{ $current_period['rest'] }}</small>
            </span>
        @endif
    </div>
    <div class="my-2">
        @if(!$school_calendar || ($school_calendars == [] && $semestre_calendars !== []))
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                <thead class="text-white text-center">
                    <th class="py-2 text-center">#ID</th>
                    <th class="">{{ $semestre_type }}</th>
                    <th>Debut</th>
                    <th>Fin</th>
                    <th>Durée</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    @foreach($semestre_calendars as $k => $s_c)
                        <tr class="text-capitalize" style="cursor:default; !important;">
                            <td class="text-center border-right" >{{ $loop->iteration }}</td>
                            </td>
                            <td class="text-center">{{ $s_c['model']->object }}</td>
                            <td class="text-center"> {{ $s_c['start'] }}</td>
                            <td class="text-center"> {{  $s_c['end'] }}</td>
                            <td class="text-center"> {{ $s_c['duration'] }}</td>
                            <td class="text-center">
                                <span class="d-flex justify-content-center">
                                    <span wire:click="editSemestrePeriods" title="Editer ce calendrier" class="fa fa-edit text-primary cursor-pointer"></span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($school_calendar)
            @if(count($school_calendars) > 0)
                <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                    <thead>
                        <th class="py-2 text-center z-bg-secondary text-uppercase" colspan="7"> 
                            <span class="fa-1x">
                                LE calendrier des {{ $school_calendar_title }}
                            </span> 
                        </th>
                    </thead>
                    <thead class="text-white text-center">
                        
                        <th class="py-2 text-center">#ID</th>
                        <th class="text-center">Objet</th>
                        <th class="">{{ $semestre_type }} concerné</th>
                        <th>Debut</th>
                        <th>Fin</th>
                        <th>Durée</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        @foreach($school_calendars as $key => $school_cal)
                            <tr class="text-capitalize" style="cursor:default; !important;">
                                <td class="text-center border-right" >{{ $loop->iteration }}</td>
                                <td class="px-1 py-1" >{{ $school_cal->object }}</td>
                                <td class="text-center border-right" >{{ $semestre_type . ' ' . $school_cal->semestre }}</td>
                                <td class="text-center border-right" >{{ $school_cal->__getDateAsString($school_cal->start, false) }}</td>
                                <td class="text-center border-right" >{{ $school_cal->__getDateAsString($school_cal->end, false) }}</td>
                                <td class="text-center border-right" >{{ $school_cal->getDuration() }}</td>
                                <td class="text-center">
                                    <span class="d-flex justify-content-around">
                                        <span wire:click="editPeriod({{$school_cal->id}})" title="Editer ce programme" class="fa fa-edit text-primary cursor-pointer"></span>
                                        <span wire:click="deletePeriod({{$school_cal->id}})" title="Supprimer ce programme" class="fa fa-trash text-danger cursor-pointer"></span>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>


            @else
                <blockquote class="text-secondary bg-secondary mx-auto small w-100">
                    <span>
                        <span class="text-white-50 text-uppercase">
                            Le CALENDRIER DES {{ $school_calendar_title }} de l'année scolaire {{ $school_year_model->school_year }} est vide. <br>
                            <small class="text-warning">Veuillez en ajouter en cliquant sur le bouton supérieur à gauche!</small>
                        </span>
                    </span>
                </blockquote>
            @endif
        @else
            <blockquote class="text-secondary bg-secondary mx-auto small w-100">
                <span>
                    <span class="text-white-50 text-uppercase">
                        Le CALENDRIER DES {{ $semestre_type }} de l'année scolaire {{ $school_year_model->school_year }} est vide. <br>
                        <small class="text-warning">Veuillez définir le calendrier scolaire en cliquant sur le bouton supérieur à gauche!</small>
                    </span>
                </span>
            </blockquote>

        @endif
    </div>
</div>

</div>