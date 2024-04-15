<div>
@if($classe)
    @if($subject_selected)
    <span class="text-white mt-2 h6">
        Gestionnaire de la présence de cette classe à l'année {{ session('school_year_selected') }} en <span class="text-warning"> {{ $subject_selected->name }}</span>
    </span>
    <span wire:click="throwPresence({{$classe->id}})" class="float-right z-scale cursor-pointer btn btn-primary border mb-2 px-2">
        Faire la présence
        <span class="bi-clock"></span>
    </span>
    @endif

    @if($pupils && count($pupils))
        <div class="w-100 m-0 p-0 mt-3">
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                <thead class="text-white text-center">
                    <th class="py-2 text-center">#ID</th>
                    <th class="">Nom et Prénoms</th>
                    <th>Absences</th>
                    <th>Retards</th>
                    <th>Actions</th>
                </thead>
                <tbody>
                    @foreach($pupils as $k => $p)
                        @if(!$p->abandonned)
                        <tr class="">
                            <td class="text-center border-right py-1">{{ $loop->iteration }}</td>
                            <td class="text-capitalize pl-2" title="charger le profil de {{$p->getName()}}">
                                <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="d-flex">
                                        <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">{{ $p->getAbsencesCounter() }}</td>
                            <td class="text-center"> {{ $p->getLatesCounter() }}</td>
                            <td class="text-center w-auto p-0">

                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>                                                     
        </div>
    @else
        <div class="my-2 p-2 text-center border rounded text-white-50">
            <h6 class="mx-auto p-3 text-white-50">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cette classe de 
                <span class="text-warning">{{ $classe ? $classe->name : 'inconnue' }}</span> 
                pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> en ce qui concerne <span class="text-warning">LES ABSENCES: LA CLASSE EST VIDE</span>!
                <blockquote class="text-info">
                    Veuillez sectionner une autre année scolaire!
                </blockquote>
            </h6>
        </div>
    @endif
@endif
</div>