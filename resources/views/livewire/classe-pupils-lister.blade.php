<div>
    @if($classe)
        <div class="w-100 my-1">
            <span wire:click="multiplePupilInsertions" class="btn bg-orange border border-white" title="Ajouter des aprrenants à cette classe">
                <span class="fa fa-user-plus"></span>
                <span>Multiple Ajout</span>
            </span> 
            <span wire:click="addNewPupilTo" class="btn btn-primary border border-white" title="Ajouter un aprrenant à cette classe">
                <span class="fa fa-user-plus"></span>
                <span>Ajouter</span>
            </span>
            <span wire:click="editClasseSubjects"  class="btn mx-2 btn-secondary border border-white" title="Editer les matières de cette classe">
                <span class="fa fa-edit"></span>
                <span>Editer</span>
            </span>
            <span wire:click="printClasseList"  class="btn mx-2 btn-info border border-white float-right" title="Imprimer la liste de cette classe...">
                <span class="fa fa-print"></span>
                <span>Impr.</span>
            </span>

        </div>
        <div class="w-100 m-0 p-0 mt-3">
        @if($pupils && count($pupils) > 0)
        <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
            <thead class="text-white text-center">
                <th class="py-2 text-center">#ID</th>
                <th class="">Nom et Prénoms</th>
                <th class="">Sexe</th>
                <th>Matricule</th>
                <th>Inscrit depuis</th>
                <th>Action</th>
            </thead>
            <tbody>
                @foreach($pupils as $k => $p)
                    <tr class="">
                        <td class="text-center border-right">{{ $loop->iteration }}</td>
                        <td class="text-capitalize pl-2">
                            <span class="d-flex w-100">
                                @if (!$editingPupilName && $p->id !== $pupil_id)
                                <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="d-flex">
                                        <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                                @endif
                                @if($editingPupilName && $p->id == $pupil_id)
                                    <form wire:submit.prevent="updatePupilName" autocomplete="off" class="my-0 d-flex p-1 cursor-pointer w-100 shadow border border-secondary">
                                    <div class="d-flex justify-between w-100 row">
                                        <div class="col-9 d-flex row m-0 p-0">
                                            <x-z-input :width="'col-6'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilFirstName')" :modelName="'pupilFirstName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                            <x-z-input :width="'col-6'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilLastName')" :modelName="'pupilLastName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                        </div>

                                        <div class="col-3 m-0 p-0 d-flex row">
                                            <span wire:click="updatePupilName" class="btn w-100 d-inline-block btn-primary border cursor-pointer">
                                                Valider
                                            </span>
                                        </div>
                                    </div>
                                    </form> 
                                @endif
                                @if (!$editingPupilName)
                                    <span title="Editer le nom de l'apprenant {{$p->firstName . ' ' . $p->lastName}}" wire:click="editPupilName({{$p->id}})" class="fa bi-pen cursor-pointer mx-2 float-right"></span>
                                @endif
                                @if($editingPupilName && $p->id == $pupil_id)
                                    <span wire:click="cancelEditingPupilName" title="Fermer la fenêtre d'édition" class="fa cursor-pointer text-danger mx-2 float-right">X</span>
                                @endif
                            </span>
                        </td>
                        <td wire:click="changePupilSexe({{$p->id}})" class="text-center cursor-pointer" title="Doublecliquer pour changer le sexe">
                            {{ $p->getSexe() }}
                        </td>
                        <td class="text-center">
                            {{ $p->matricule }}
                        </td>
                        <td class="text-center">
                            {{ str_ireplace("Il y a ", '', $p->getDateAgoFormated(true)) }}
                        </td>
                        @if(!$editingPupilName)
                            <td class="text-center w-auto p-0">
                                <span class="row w-100 m-0 p-0">
                                    @if ($p->deleted_at)
                                        <span title="Supprimer définivement {{$p->name}} de la base de donnée" wire:click="forceDeleteAUser({{$p->id}})" class="text-danger col-4 m-0 p-0 cursor-pointer">
                                            <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                        </span>
                                        <span title="Restaurer {{$p->name}}" wire:click="restoreAUser({{$p->id}})" class="text-success col-4 m-0 p-0 cursor-pointer border-right border-left">
                                            <span class="fa fa-reply py-2 px-2"></span>
                                        </span>
                                        <span title="Débloquer {{$p->name}}" wire:click="unblockAUser({{$p->id}})" class="text-success col-4 m-0 p-0 cursor-pointer">
                                            <span class="fa fa-unlock py-2 px-2"></span>
                                        </span>
                                    @else
                                        <span title="Supprimer définivement {{$p->name}} de la base de donnée" wire:click="forceDeletePupil({{$p->id}})" class="text-danger col-3 m-0 p-0 cursor-pointer">
                                            <span class="text-danger cursor-pointer fa fa-trash py-2 px-2"></span>
                                        </span>
                                        <span title="Envoyer {{$p->name}} dans la corbeile" wire:click="deletePupil({{$p->id}})" class="text-warning border-right border-left col-3 m-0 p-0 cursor-pointer">
                                            <span class="cursor-pointer fa fa-trash py-2 px-2"></span>
                                        </span>
                                        <span title="Bloquer {{$p->name}}" class="text-info col-3 m-0 p-0 cursor-pointer border-right">
                                            <span class="fa fa-lock py-2 px-2"></span>
                                        </span>
                                    @endif
                                </span>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>   
        @endif
        @if(!$pupils)
            <div class="my-2 p-2 text-center border rounded">
                <h6 class="mx-auto p-3">
                    <h1 class="m-0 p-0">
                        <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                    </h1>
                    Il parait qu'aucune donnée n'est disponible pour cette classe de 
                    <span class="text-warning">{{ session('classe_selected') }}</span> 
                    pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                    

                    <blockquote class="text-info">
                        Veuillez sectionner une autre année scolaire
                    </blockquote>
                </h6>
            </div>
        @endif
    @endif                                                
</div>
</div>
