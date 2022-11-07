<div>
    @if($classe)
    <div>
        <h6 class="m-0 p-0 py-1 rounded text-white-50 shadow border border-secondary d-flex justify-content-between">
            <span class="pt-2 pl-2">
                Fiche des santions de la classe de <span class="text-warning">{{ $classe->name }} </span> au cours de l'année scolaire {{ session('school_year_selected')}}
            </span>

            <span wire:click="deleteAllRelatedMarks" title="Ajouter une note relative : Sanction ou Bonus" class="float-right btn btn-primary mr-2 border">
                <span class="bi-trash text-orange"></span>
                <span class="ml-1">Vider</span>
            </span>
        </h6>
    </div>
    <div class="w-100 m-0 p-0 mt-3">
    @if(count($pupils))
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Apprenants</th>
                <th>Bonus</th>
                <th>Sanctions</th>
                <th>Dernière</th>
                <th>Date</th>
                <th>Horaire</th>
                <th>Actions</th>
            </thead>
            <tbody>
                @foreach($pupils as $p)
                    <tr class="">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2" >
                            {{ $p->getName() }}
                        </td>
                        
                        <td class="text-center text-success"> {{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'bonus', true) }}</td>
                        <td class="text-center text-danger"> {{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'minus', true) }}</td>
                        <td class="text-center"> {{ $p->getLastRelatedMarkValue($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), true) ? $p->getLastRelatedMarkValue($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), true) : '-'}}</td>
                        <td class="text-center text-capitalize"> {{ $p->getLastRelatedMarkDate($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) ? $p->getLastRelatedMarkDate($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) : '-' }}</td>
                        <td class="text-center text-capitalize"> {{ $p->getLastRelatedMarkHoraire($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) ? $p->getLastRelatedMarkHoraire($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) : '-' }}</td>
                        <td class="text-center"> 
                            <span class="row w-100 m-0 p-0">
                                <span title="Supprimer la Dernière note" class="col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-warning cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                <span wire:click="deleteAllPupilRelatedMarks({{$p->id}})" title="Supprimer toutes les notes" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                    <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                </span>
                                <span wire:click="insertRelatedMark({{$p->id}})" title="Faire un bonus ou une sanction à {{$p->getName()}}" class="text-danger col-4 m-0 p-0 cursor-pointer">
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
                    La fiche des notes relatives de <span class="text-warning">{{ $classe->name }} </span> au cours de l'année scolaire {{ session('school_year_selected')}} est viège. <br>
                    
                </h6>
            </blockquote>
        </div>
    @endif                                         
    </div>
    @endif
</div>