<div>
    @if($classe)
        <div class="w-100 my-1 mt-2">
            @if(auth()->user()->isAdminAs('master'))
                <span wire:click="multiplePupilInsertions" class="btn bg-orange z-scale border border-white" title="Ajouter des aprrenants à cette classe">
                    <span class="fa fa-user-plus"></span>
                    <span>Multiple Ajout</span>
                </span> 
                <span wire:click="addNewPupilTo" class="btn z-scale mx-2 btn-primary border border-white" title="Ajouter un aprrenant à cette classe">
                    <span class="fa fa-user-plus"></span>
                    <span>Ajouter</span>
                </span>
                <span wire:click="editClasseSubjects"  class="btn mx-2 z-scale btn-secondary border border-white" title="Editer les matières de cette classe">
                    <span class="fa fa-edit"></span>
                    <span>Editer les matières</span>
                </span>
                <span wire:click="importPupilsIntoClasse" class="btn mx-2 btn-success border border-white z-scale" title="Importer des aprrenants">
                    <span class="fa fa-download"></span>
                    <span>Importer des apprenants</span>
                </span>
            @endif
            @if($pupils && count($pupils))
            <span wire:click="downloadPDF" class="z-scale btn mx-2 btn-info border border-white float-right" title="Imprimer la liste de cette classe...">
                <span class="fa fa-print"></span>
                <span>Impr.</span>
            </span>
            @endif
        </div>
        @if($is_loading)
            <div class="w-100 d-flex justify-content-center flex-column">
                @livewire('solar-system-loader')  
            </div>
        @else
        <div class="w-100 m-0 p-0 mt-3">
        @if($pupils && count($pupils) > 0)
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table text-white">
                <thead class="text-white text-center">
                    <th class="py-2 text-center">#ID</th>
                    <th class="">Nom et Prénoms</th>
                    <th class="">Sexe</th>
                    <th>Matricule</th>
                    <th>Inscrit depuis</th>
                    <th> + Actions</th>
                    <th>Retirer.</th>
                    <th>Suppr.</th>
                </thead>
                <tbody>
                    @foreach($pupils as $k => $p)
                        <tr class="">
                            <td class="text-center border-right">{{ $loop->iteration }}</td>
                            <td @if($editingPupilName && $pupil_id == $p->id) colspan="6" @endif class="text-capitalize pl-2">
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
                                        <form wire:submit.prevent="updatePupilName" autocomplete="off" class="my-0 d-flex p-1 cursor-pointer w-100 shadow table align-middle m-2">
                                        <div class="d-flex justify-between px-1 align-middle table w-100 row m-0 p-0">
                                            <div class="col-9 d-flex align-middle justify-content-between row m-0 p-0 px-1">
                                                <x-z-input :width="'col-5 m-0'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilFirstName')" :modelName="'pupilFirstName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                                <x-z-input :width="'col-6 m-0'" :hideLabel="'d-none'" :type="'text'" :error="$errors->first('pupilLastName')" :modelName="'pupilLastName'" :labelTitle="'Le Nom de la classe'" ></x-z-input>
                                            </div>

                                            <div class="col-2 m-0 p-0 align-middle d-flex row">
                                                <span wire:click="updatePupilName" class="btn w-100 d-inline-block btn-primary table py-1 mt-1 border p-0 cursor-pointer">
                                                    <span class="mt-2">
                                                        <span>OK</span>
                                                        <span class="fa fa-check"></span>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        </form> 
                                    @elseif($editingPupilName && $pupil_id !== $p->id)
                                    <a class="text-white w-100 m-0 p-0" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                        <span class="d-flex">
                                            <img width="23" class="border rounded-circle my-1" src="{{$p->__profil(110)}}" alt="photo de profil">
                                            <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                                {{$p->getName()}}
                                            </span>
                                        </span>
                                    </a>
                                    @endif
                                    @if (!$editingPupilName)
                                        <span title="Editer le nom de l'apprenant {{$p->firstName . ' ' . $p->lastName}}" wire:click="editPupilName({{$p->id}})" class="fa bi-pen cursor-pointer mx-2 float-right"></span>
                                    @endif
                                    @if($editingPupilName && $p->id == $pupil_id)
                                        <span wire:click="cancelEditingPupilName" title="Fermer la fenêtre d'édition" class="fa cursor-pointer text-danger mx-2 p-2 m-2 float-right">X</span>
                                    @endif
                                </span>
                            </td>
                            @if(!$editingPupilName || $editingPupilName && $pupil_id !== $p->id)
                            <td wire:click="changePupilSexe({{$p->id}})" class="text-center cursor-pointer" title="Doublecliquer pour changer le sexe">
                                {{ $p->getSexe() }}
                            </td>
                            <td class="text-center">
                                {{ $p->matricule }}
                            </td>
                            <td class="text-center">
                                {{ str_ireplace("Il y a ", '', $p->getDateAgoFormated(true)) }}
                            </td>
                            <td class="text-center w-auto p-0">
                                <span class="row w-100 m-0 p-0">
                                    @if ($p->inPolyvalenceClasse())
                                        <span title="Définir la nouvelle classe de  l'apprenant {{$p->name}}" wire:click="migrateTo({{$p->id}})" class="text-danger col-12 m-0 p-0 cursor-pointer">
                                            <span class="text-primary cursor-pointer fa bi-tools py-2 px-2"></span>
                                        </span>
                                    @else
                                        @if($p->canUpdateMarksOfThisPupil())
                                            <span title="Verrouiller édtion des notes de l'apprenant {{$p->name}}" wire:click="lockMarksUpdating({{$p->id}})" class="text-danger border-right col-4 m-0 p-0 cursor-pointer">
                                                <span class="cursor-pointer fa fa-lock py-2 px-2"></span>
                                            </span>
                                        @else
                                            <span title="déverrouiller édtion des notes de l'apprenant {{$p->name}}" wire:click="unlockMarksUpdating({{$p->id}})" class="text-success border-right col-4 m-0 p-0 cursor-pointer">
                                                <span class="cursor-pointer fa fa-unlock py-2 px-2"></span>
                                            </span>

                                        @endif

                                        @if($p->canInsertOrUpdateMarksOfThisPupil())
                                            <span wire:click="lockMarksInsertion({{$p->id}})" title="Verrouiller la gestion des notes de l'apprenant {{$p->name}}" class="text-info col-4 m-0 p-0 cursor-pointer border-right">
                                                <span class="fa fa-lock py-2 px-2"></span>
                                            </span>

                                        @else
                                            <span wire:click="unlockMarksInsertion({{$p->id}})" title="déverrouiller la gestion des notes de l'apprenant {{$p->name}}" class="text-info col-4 m-0 p-0 cursor-pointer border-right">
                                                <span class="fa fa-unlock py-2 px-2"></span>
                                            </span>
                                        @endif
                                        <span wire:click="migrateTo({{$p->id}})" title="Faire migrer l'apprenant {{$p->name}} vers une nouvelle classe" class="text-success col-4 m-0 p-0 cursor-pointer">
                                            <span class="fa fa-recycle py-2 px-2"></span>
                                        </span>
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <span wire:click="detachPupil({{$p->id}})" title="Retirer l'apprenant {{$p->name}} de cette classe et de cette année-scolaire c'est-à-dire désinscrire cet apprenant!" class="text-warning m-0 p-0 cursor-pointer">
                                    <span class="fa fa-trash py-2 px-2"></span>
                                </span>
                            </td>
                            <td class="text-center">
                                <span wire:click="forceDelete({{$p->id}})" title="Supprimer définitivement l'apprenant {{$p->name}}" class="text-danger m-0 p-0 cursor-pointer">
                                    <span class="fa fa-trash py-2 px-2"></span>
                                </span>
                            </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>  
        @else
            <div>
                <div class="d-flex justify-content-center mx-auto mt-4  w-100">
                    <span class="fa fa-trash text-muted fa-8x"></span>
                </div>
                <div class="d-flex justify-content-center mx-auto mt-3 w-100">
                    <h4 class="letter-spacing-12 font-italic text-orange">OUUUPPPS, la classe est vide!!!</h4>
                </div>
                <blockquote class="text-warning">
                    <span class="float-right border-top border-white w-100 d-inline-block text-right">
                        <i class="text-warning small">OUPPPS la classe est vide!!!!!</i>
                    </span>
                </blockquote>
            </div>
        @endif
        @if(!$pupils)
            <div class="my-2 p-2 text-center border rounded text-white-50">
                <h6 class="mx-auto p-3 text-white-50">
                    <h1 class="m-0 p-0">
                        <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                    </h1>
                    Il parait qu'aucune donnée n'est disponible pour cette classe de 
                    <span class="text-warning">{{ $classe ? $classe->name : 'inconnue' }}</span> 
                    pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                    

                    <blockquote class="text-info">
                        Veuillez sectionner une autre année scolaire
                    </blockquote>
                </h6>
            </div>
        @endif
        @endif
    @endif                                                
</div>
</div>
