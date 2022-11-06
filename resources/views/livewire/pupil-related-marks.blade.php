<div>
    @if($pupil)
    <div>
        <h6 class="m-0 p-0 py-1 rounded text-white-50 shadow border border-secondary d-flex justify-content-between">
            <span class="pt-2 pl-2">
                Fiche des santions de <span class="text-warning">{{ $pupil->getName()}} </span> au cours de l'année scolaire {{ session('school_year_selected')}}
            </span>

            <span wire:click="insertRelatedMark" title="Ajouter une note relative : Sanction ou Bonus" class="float-right btn btn-primary mr-2 border">
                <span class="bi-plus"></span>
                <span class="ml-1">Ajouter</span>
            </span>
        </h6>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($related_marks))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Matières</th>
                <th>Date</th>
                <th>Horaire</th>
                <th>Note</th>
                <th>Type</th>
                <th>Motif</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($related_marks as $k => $mark)
                    <tr class="@if($mark->justified) text-success @else text-danger @endif">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2" >
                            {{ $mark->subject->name }}
                        </td>
                        
                        <td class="text-center">{{ $mark->date }}</td>
                        <td class="text-center"> {{ $mark->horaire }}</td>
                        <td class="text-center"> {{ $mark->coming_hour }}</td>
                        <td class="text-center"> {{ $mark->duration }}</td>
                        <td class="text-center"> {{ $mark->motif }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer ce retard" wire:click="delete({{$mark->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                @if($mark->justified)
                                    <span title="Marquer comme non justifié" wire:click="unjustified({{$mark->id}})" class="text-warning col-4 m-0 p-0 cursor-pointer border-right border-left">
                                    <span class="fa bi-person-x-fill py-2 px-2"></span>
                                </span>
                                @else
                                    <span title="Marquer comme justifié" wire:click="justified({{$mark->id}})" class="text-success col-4 m-0 p-0 cursor-pointer border-right border-left">
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
                    La fiche des notes relatives de <span class="text-warning">{{ $pupil->getName()}} </span> au cours de l'année scolaire {{ session('school_year_selected')}} est viège. <br>
                    Il est donc fort probable que ce dernier n'a encore enregistré aucune comportement particulier.
                </h6>
                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                    <span class="fa bi-clock text-success"></span>
                    <span class="fa bi-clock text-success"></span>
                    <span class="fa bi-clock text-success"></span>
                    <i class="text-warning small">On a donc affaire à un apprenant plus ou moins timide!!!</i>
                </span>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>