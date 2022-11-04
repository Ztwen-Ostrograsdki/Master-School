<table class="table-table table-striped w-100 classes-marks">
    <thead>
        <th class="no-tag">No</th>
        <th class=" pupils-tag">Elèves</th>
        <th class="subjects-tag">
            <span>Les notes
                <span class="d-inline-block m-0 p-0" >
                    <span class="h5-title text-white-50 m-0 p-0" >
                    Seulement les 3 meilleurs notes sont prises en comptes
                </span>
                <span class="h5-title text-white-50 m-0 p-0" >
                    Toutes les notes sont prises en comptes
                </span>
                </span>
            </span>
            <span class="float-right mr-1 d-flex flex-column justify-content-between">
                <span class="fa fa-check text-success mb-2" title="Lancer les modalités" ></span>
                <span class="fa fa-mail-reply text-info" title="Annuler la proccédure"></span>
            </span>
            <span class="float-left ml-1 d-flex flex-column justify-content-center">
                <span class="fa fa-close text-danger mb-2" title="Annuler toutes les modalités"></span>
            </span>
            <span class="fa fa-filter float-right m-0 p-0 mr-2" title=" Définisser la modalité de calcule des moyennes"></span>
                <span class="float-right m-0 p-0 mr-1">
                    <form id="classe-modality" class="d-inline opac-form m-0 p-0">
                        <input style="color: orange" class="form-control m-0 p-0 text-center" type="number" name="modalityLength" title="Veuillez renseigner le nombre de notes à prendre en compte">
                    </form>
                </span>
            <span class="h5-title text-warning d-block p-0 m-0">
                Indiquer le nombre de notes à prendre en compte
            </span>
            <span  class="h5-title d-block p-0 m-0">
                bebbebe
            </span>
        </th>
        <th class="subjects-tag">
            <span>Moyennes</span>
            <span class="fa fa-desktop float-right mr-2" title="calculer les moyennes maintenant"></span>
        </th>
        <th class="actions-tag">Classer</th>
    </thead>
    <tbody class="w-100 marks-td">
        <tr class="border-bottom border-white">
            <td class=" bg-linear-official-180">x</td>
            <td class=" bg-linear-official-180">x</td>
            <td class="text-center ">
                <table class="text-center w-100 text-white-50">
                    <tbody class="w-100 notes">
                        <tr class="text-center w-100">
                            <td colspan="{{$epeMaxLenght}}">Les interrogations</td>
                            <td class="" colspan="{{$devMaxLenght}}">Les devoirs</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td class="text-center">
                <table class="text-center w-100 text-white-50">
                    <tbody class="w-100 moyennes">
                        <tr class="text-center w-100">
                            <td class="text-center text-success">Moy</td>
                            <td class="text-center text-warning">Moy Coef</td>
                            <td class="text-center text-info">Rang</td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <span class="fa fa-recycle" ></span>
            </td>
        </tr>
        <tr class="border-bottom border-white-50">
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
                    <tbody class="w-100 notes">
                        @foreach($pupils as $k => $p)
                        <tr class="w-100">
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            <td>
                <table class="w-100">
                    <tbody class="w-100">
                        <tr class="w-100">
                            <td class="text-success"> - </td>
                            <td class="text-warning"> - </td>

                            <td class="text-info" v-if="getRange(k + 1, getAverage(pupil.id, targetedClasseMarks, targetedClasseSubjectsCoef, targetedClasseSubject).avg) !== '-'">
                                2eme
                            </td>
                            <td class="text-info" v-if="getRange(k + 1, getAverage(pupil.id, targetedClasseMarks, targetedClasseSubjectsCoef, targetedClasseSubject).avg) == '-'">
                                -
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <span class="fa fa-recycle text-muted"></span>
            </td>
        </tr>
    </tbody>
</table>