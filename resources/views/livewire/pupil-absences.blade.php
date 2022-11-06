<div>
    @if($pupil)
    <div>
        <blockquote class="text-warning">
            <h6 class="m-0 p-0 h6 text-white-50">
                Fiche des absences de <span class="text-warning">{{ $pupil->getName()}} </span> au cours de l'année scolaire {{ session('school_year_selected')}}

                <span class="float-right text-muted"> {{ count($absences) }} absences enregistrés</span>
            </h6>
        </blockquote>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($absences))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Matières</th>
                <th>Date</th>
                <th>Horaire</th>
                <th>Motif</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach($absences as $k => $absence)
                    <tr class="@if($absence->justified) text-success @else text-danger @endif">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2" >
                            {{ $absence->subject->name }}
                        </td>
                        
                        <td class="text-center">{{ $absence->__getDateAsString($absence->date) }}</td>
                        <td class="text-center"> {{ $absence->horaire }}</td>
                        <td class="text-center"> {{ $absence->motif }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer cette absence" wire:click="delete({{$absence->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                @if($absence->justified)
                                    <span title="Marquer comme non justifié" wire:click="unjustified({{$absence->id}})" class="text-warning col-4 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-person-x-fill py-2 px-2"></span>
                                </span>
                                @else
                                    <span title="Marquer comme justifié" wire:click="justified({{$absence->id}})" class="text-success col-4 m-0 p-0 cursor-pointer border-right border-left">
                                        <span class="fa fa-check py-2 px-2"></span>
                                    </span>
                                @endif
                                <span title="Editer" wire:click="edit({{$absence->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
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
                    La fiche des absences de <span class="text-warning">{{ $pupil->getName()}} </span> au cours de l'année scolaire {{ session('school_year_selected')}} est viège. <br>
                    Il est donc fort probable que ce dernier n'a encore enregistré aucune absence.
                </h6>
                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                    <span class="fa fa-heart text-danger"></span>
                    <span class="fa fa-heart text-danger"></span>
                    <span class="fa fa-heart text-danger"></span>
                    <i class="text-warning small">On a donc affaire à un apprenant ponctuel et exemplaire!!!</i>
                </span>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>