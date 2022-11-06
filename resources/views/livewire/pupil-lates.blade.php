<div>
    @if($pupil)
    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50">
                Fiche des retard de <span class="text-warning">{{ $pupil->getName()}} </span> au cours de l'année scolaire {{ session('school_year_selected')}}

                <span class="float-right text-muted"> {{ count($lates) }} retards enregistrés</span>
            </h6>
        </blockquote>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($lates))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Matières</th>
                <th>Date</th>
                <th>Horaire</th>
                <th>Heure d'arrivée</th>
                <th>Retard en min</th>
                <th>Motif</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($lates as $k => $late)
                    <tr class="@if($late->justified) text-success @else text-danger @endif">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2" >
                            {{ $late->subject->name }}
                        </td>
                        
                        <td class="text-center">{{ $late->__getDateAsString($late->date) }}</td>
                        <td class="text-center"> {{ $late->horaire }}</td>
                        <td class="text-center"> {{ $late->coming_hour }}</td>
                        <td class="text-center"> {{ $late->duration }}</td>
                        <td class="text-center"> {{ $late->motif }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer ce retard" wire:click="delete({{$late->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                @if($late->justified)
                                    <span title="Marquer comme non justifié" wire:click="unjustified({{$late->id}})" class="text-warning col-4 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-person-x-fill py-2 px-2"></span>
                                </span>
                                @else
                                    <span title="Marquer comme justifié" wire:click="justified({{$late->id}})" class="text-success col-4 m-0 p-0 cursor-pointer border-right border-left">
                                        <span class="fa fa-check py-2 px-2"></span>
                                    </span>
                                @endif
                                <span title="Editer" wire:click="edit({{$alte->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-primary cursor-pointer fa fa-edit py-2 px-2"></span>
                                </span>
                            </span>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>            
    @else
        <div>
            <blockquote class="">
                <h6 class="h6 text-white-50">
                    La fiche des retards de <span class="text-warning">{{ $pupil->getName()}} </span> au cours de l'année scolaire {{ session('school_year_selected')}} est viège. <br>
                    Il est donc fort probable que ce dernier n'a encore enregistré aucun retard.
                </h6>
                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                    <span class="fa bi-clock text-success"></span>
                    <span class="fa bi-clock text-success"></span>
                    <span class="fa bi-clock text-success"></span>
                    <i class="text-warning small">On a donc affaire à un apprenant ponctuel et exemplaire!!!</i>
                </span>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>