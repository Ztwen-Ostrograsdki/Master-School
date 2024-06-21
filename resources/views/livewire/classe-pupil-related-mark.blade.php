<div>
    @if($classe)
    <div>
        <h6 class="m-0 p-0 py-1 rounded text-white-50 shadow border border-secondary d-flex justify-content-between">
            <span class="pt-2 pl-2">
                Fiche des santions de la classe de <span class="text-warning">{{ $classe->name }} </span> au cours de l'année scolaire {{ session('school_year_selected')}}
            </span>

            @if($not_stopped)
                <span class="justify-content-between">
                    <span wire:click="refreshClasseRelatedsMarks({{$classe->id}})" title="Supprimer toutes les notes de participation - Bonus - Sanctions" class="float-right z-scale btn btn-primary mr-2 border">
                        <span class="bi-trash text-orange"></span>
                        <span class="ml-1">Vider</span>
                    </span>
                    <span wire:click="makeRelatedMarkTogether({{$classe->id}})" title="Ajouter une note collective à toute la classe de {{$classe->name}} : Sanction ou Bonus à toute la classe" class="float-right btn btn-warning mr-2 border z-scale">
                        <span class="ml-1 text-dark">
                            <span>+ / -</span> 
                            <span class="fa-people"></span>
                            <span class="bi-person text-dark"></span>
                        </span>
                    </span>
                </span>
            @endif
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
                @if($not_stopped)
                    <th>Actions</th>
                @endif
            </thead>
            <tbody>
                @foreach($pupils as $p)
                    @if(!$p->abandonned)
                        <tr class="">
                            <td class="text-center border-right">{{ $loop->iteration }}</td>
                            <td class="text-capitalize pl-2 @if($p->sexe == 'female') text-orange  @endif " >
                                {{ $p->getName() }}
                            </td>
                            
                            <td class="text-center text-success"> {{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'bonus', true) }}</td>
                            <td class="text-center text-danger"> {{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'minus', true) }}</td>
                            <td class="text-center"> {{ $p->getLastRelatedMarkValue($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), true) ? $p->getLastRelatedMarkValue($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), true) : '-'}}</td>
                            <td class="text-center text-capitalize"> {{ $p->getLastRelatedMarkDate($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) ? $p->getLastRelatedMarkDate($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) : '-' }}</td>
                            <td class="text-center text-capitalize"> {{ $p->getLastRelatedMarkHoraire($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) ? $p->getLastRelatedMarkHoraire($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected')) : '-' }}</td>
                            @if($not_stopped)
                                <td class="text-center"> 
                                    <span class="row w-100 m-0 p-0">
                                        <span title="Supprimer la Dernière note" class="col-4 m-0 p-0 cursor-pointer">
                                            <span class="text-warning cursor-pointer fa fa-trash py-2 px-2"></span>
                                        </span>
                                        <span wire:click="refreshPupilRelatedsMarks({{$p->id}})" title="Supprimer toutes les notes bonus - sanctions de l'apprenant {{$p->getName()}}" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                            <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                        </span>
                                        <span wire:click="insertRelatedMark({{$p->id}})" title="Faire un bonus ou une sanction à {{$p->getName()}}" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                            <span class="text-primary cursor-pointer fa fa-edit py-2 px-2"></span>
                                        </span>
                                    </span>
                                </td>
                            @endif

                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>            
    @else
        <div class="my-2 p-2 text-center border rounded text-white-50">
            <h6 class="mx-auto p-3 text-white-50">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cette classe de 
                <span class="text-warning">{{ $classe ? $classe->name : 'inconnue' }}</span> 
                pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> en ce qui concerne <span class="text-warning">LES NOTES RELATIVES : BONUS ET OU SANCTIONS</span>!
                <blockquote class="text-info">
                    Veuillez sectionner une autre année scolaire!
                </blockquote>
            </h6>
        </div>
    @endif                                         
    </div>
    @endif
</div>