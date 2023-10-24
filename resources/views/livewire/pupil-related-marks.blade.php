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
                    <tr class="">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2" >
                            {{ $mark->subject->name }}
                        </td>
                        
                        <td class="text-center text-capitalize">{{ $mark->__getDateAsString($mark->date) }}</td>
                        <td class="text-center"> {{ $mark->horaire }}</td>
                        <td class="text-center @if($mark->type == 'bonus') text-success @else text-danger @endif"> {{ $mark->getValue() }}</td>
                        <td class="text-center"> {{ $mark->type }}</td>
                        <td class="text-center"> {{ $mark->motif }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer cette note" wire:click="delete({{$mark->id}})" class="text-danger col-6 m-0 p-0 cursor-pointer border-right">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                <span title="Editer cette note" wire:click="editRelatedMark({{$mark->id}})" class="text-danger col-6 m-0 p-0 cursor-pointer">
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
                    Il est donc fort probable que ce dernier n'a encore enregistré aucun comportement particulier.
                </h6>
                <span class="float-right border-top border-white w-100 d-inline-block text-right">
                    <i class="text-warning small">Un apprenant particulièrement timide peut-être!!!</i>
                </span>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>