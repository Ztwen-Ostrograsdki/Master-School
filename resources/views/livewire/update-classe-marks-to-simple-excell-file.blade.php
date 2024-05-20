<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="8" :icon="'fa fa-book'" :modalName="'updateClasseMarksToSimpleExcelFileModal'" :modalBodyTitle="$title">
    @if($classe)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" enctype="multipart/form-data">
        <div class="row justify-between w-100">
            <div class="mt-0 mb-2 col-12 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info rounded mx-auto w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Classe (e) : 
                        <span class="text-warning">
                            {{$classe->name}} 
                        </span>

                        <span title="Vider le formulaire" wire:click="clearForm" class="z-scale border cursor-pointer float-right btn btn-primary">
                            <span class="px-3">EFFACER</span>
                        </span>
                    </blockquote>
                </div>
               
            </div>
        </div>

        <div class="m-0 p-0 w-100 bg-transparent py-2 p-3 mx-auto col-12 row border border-dark border-0-cyan border-1-cyan d-flex justify-content-center">
            <div class=" col-12 mx-auto border">
                <div class="m-0 p-0 w-100 my-2 p-2">
                        @if($target_row)
                            <div class="w-100 d-flex justify-content-center p-2">
                                <h6>
                                    <span class="text-white-50 small">Donnée du fichier sélectionnée</span>
                                    <span class="text-white-50">
                                        APRRENANT: 
                                        <span class="text-warning">
                                            {{ $target_row['firstName']  . ' ' . $target_row['lastName']}}
                                        </span>
                                        <span class="text-orange">({{$target_row['ltpk_matricule']}})</span>
                                    </span>
                                </h6>
                                @if($pupil)
                                    <h6 class="text-white-50 px-2 py-1 border border-warning rounded">
                                        <span class="small">Transféré les données vers les données de </span>
                                        <span class="text-success">
                                            {{ $pupil->getName() }}
                                        </span>
                                        <small class="text-warning ml-1 font-italic letter-spacing-12">
                                            {{$pupil->ltpk_matricule}}
                                        </small>
                                    </h6>
                                @else
                                    <span class="text-orange small">Veuillez sélectionner l'apprenant cible!</span>
                                @endif
                            </div>
                        @endif
                        <div class="d-flex justify-between">
                            <div class="col-4">
                                <select wire:model="target" class="form-select custom-select border border-warning">
                                    <option value="{{null}}">Veuillez sélectionner la cible</option>
                                    @foreach ($targets as $tg => $val)
                                        <option value="{{$tg}}">{{ $val }}</option>
                                    @endforeach
                                </select>
                                @error('target')
                                    <small class="text-danger">{{$message}}</small>
                                @enderror
                            </div>
                            @if($target && $target == 'pupil')

                                <div class="col-6">
                                    <select wire:model="pupil_id" class="form-select custom-select border border-warning">
                                        <option value="{{null}}">Veuillez sélectionner l'apprenant à cibler</option>
                                        @foreach ($pupils as $p)
                                            <option value="{{$p->id}}">{{ $p->getName() }}</option>
                                        @endforeach
                                    </select>
                                    @error('pupil_id')
                                        <small class="text-danger">{{$message}}</small>
                                    @enderror
                                </div>

                            @endif
                        </div>
                        @if($target && $target == 'pupil')
                            <div class="w-100 py-2 my-2 d-block" style="max-height: 170px; overflow: auto;">
                                <div class="m-2 border rounded p-1">
                                    <h6 title="Cliquer pour masquer ou afficher la liste du fichier" wire:click="toShowOrHide" class="w-100 cursor-pointer">
                                        <span class="p-1">
                                            <span class="text-white-50">Les données du fichier chargé</span>  
                                            <span class="text-warning"> 
                                                ({{count($pupils_data)}} données trouvées!)
                                            </span>
                                        </span>
                                        <span class="float-right px-2 mr-2 fx-18 cursor-pointer z-scale" title="Cliquer pour masquer ou afficher les données chargées" wire:click="toShowOrHide">
                                            @if(!$show)
                                                <span class="fa fa-eye z-scale"></span>
                                            @else
                                                <span class="fa fa-chevron-down z-scale"></span>
                                            @endif
                                        </span>
                                        <hr class="bg-secondary w-100 m-0 ">
                                    </h6>
                                    <table class="w-100 m-0 @if(!$show) d-none @endif p-0 table-striped table-bordered z-table text-white">
                                        <col>
                                        <col>
                                        <col>
                                        <tr class="text-center bg-secondary-light-1 ">
                                            <th>No</th>
                                            <th>Nom et Prénoms </th>
                                            <th>Matricule</th>
                                        </tr>
                                        @foreach($pupils_data as $pd)
                                            <tr  class="text-left text-center small" wire:click="getTargetRowData({{$loop->iteration - 1}})">
                                                <th scope="row" class="text-center py-1">{{ $loop->iteration }}</th>
                                                <th class="@if($target_row && $row == $loop->iteration - 1) text-warning @endif text-capitalize text-center pl-2 p-0 m-0">
                                                    <span class="d-flex justify-content-between">
                                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small ">
                                                            {{ $pd['firstName'] . ' ' . $pd['lastName'] }}
                                                        </span>

                                                        @if($target_row && $row == $loop->iteration - 1) 
                                                            <span class="fa fa-check mr-2 mt-1 fx-18 text-success"></span>
                                                        @endif
                                                        
                                                    </span>
                                                </th>
                                                <th class="text-capitalize text-center p-0 m-0 px-2">
                                                    {{$pd['ltpk_matricule']}}
                                                </th>
                                            </tr>
                                        @endforeach
                                    </table> 
                                    @if($pupils_data && $show && count($pupils_data) > 10)
                                        <span class="float-right cursor-pointer my-2 fx-20 px-2 mr-2 z-scale" title="Cliquer pour masquer ou afficher les données chargées" wire:click="toShowOrHide">
                                            @if(!$show)
                                                <span class="fa fa-eye z-scale"></span>
                                            @else
                                                <span class="fa fa-chevron-down z-scale"></span>
                                            @endif
                                        </span>
                                        <hr class="bg-secondary w-100 m-0 "> 
                                    @endif                                     
                                </div>
                            </div>
                        @endif

                        @if($show_form)
                            <div>
                                <label title="Cliquer pour masquer le formulaire" wire:click="toShowOrHideForm" class="text-orange">Selectionner votre fichier</label>
                                <span wire:click="toShowOrHideForm" class="fa z-scale mr-2 text-orange mt-2 cursor-pointer fx-18 fa-chevron-down float-right" title="Masquer le formulaire"></span>
                                <input placeholder="Sélectionner votre fichier" class="form-control bg-transparent text-white letter-spacing-12 text-italic border border-white" id="data_file" type="file" wire:model="data_file">
                            </div>

                            <div class="my-3 p-2 d-flex justify-content-start flex-column">
                                <span class="ml-3 text-warning letter-spacing-12 text-italic text-center" wire:loading wire:target="data_file" >Chargement en cours, veuillez patienter...</span>
                                @if($data_file)
                                    <span class="m-2 d-flex border rounded">
                                        <table class="m-0 w-100 table-striped table-bordered z-table text-white text-center p-3" style="">
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                            <col>
                                            <tr style="letter-spacing: 1.2px;" class="bg-secondary-light-0 p-2">
                                                <th  class="py-2">
                                                    Fichier Excel
                                                </th>
                                                <th class="text-center" >
                                                    <img class="text-center mx-auto" width="30" src="{{asset('icons/unuse-file.png')}}" alt="une image">
                                                </th>
                                                <th >
                                                    <span class="text-white-50 font-italic letter-spacing-12">
                                                        <small>{{ mb_substr($data_file->getClientOriginalName(), 0, 25) }}</small>
                                                    </span>
                                                </th>
                                                <th >
                                                    Taille: 
                                                    <span class="text-white-50 font-italic letter-spacing-12">
                                                        <small>{{ number_format(($data_file->getSize() / 1000), 2) }} Ko</small>
                                                    </span>
                                                </th>
                                                <th >
                                                    <span class="text-white-50 font-italic letter-spacing-12">
                                                        <small> .{{ $data_file->extension() }}</small>
                                                    </span>
                                                </th>
                                            </tr>

                                        </table>                                            
                                    </span>
                                @else
                                    <span class="text-cyan text-center font-italic fx-20 letter-spacing-12 ">Aucun fichier sélectionné</span>
                                @endif
                            </div>

                            <div class="my-1 p-2 d-flex justify-content-start flex-column">
                                @error('data_file')
                                    <small class="text-danger">{{$message}}</small>
                                @enderror
                            </div>
                        @else
                            <h5 wire:click="toShowOrHideForm" class="cursor-pointer text-center text-orange z-scale w-100 p-2" title="Cliquer pour afficher le formulaire">Afficher le formulaire</h5>
                        @endif

                        @if($data_file)
                        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                            <span   wire:click="importedData" class="text-dark border border-white rounded bg-primary h6 text-center cursor-pointer col-7 z-scale py-2 px-3 ">
                                <span>Mettre à jour les données</span>
                                <span class=" fa fa-upload mx-2"></span>
                            </span>

                        </div>
                        @else
                        <div style="opacity: 0.4;" class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                            <strong  class="text-dark border h6 border-warning rounded bg-secondary text-center cursor-pointer col-7 py-2 px-3 ">Veuillez sélectionner votre fichier</strong>
                        </div>
                        @endif
                        <div class="p-0 m-0 my-1 py-1">
                            <span class="text-warning d-block m-0 p-0 text-center">ATTENTION !!!</span>
                            <span class="text-orange fx-12 text-center">Les actions à effectuer sont irreversibles! Veuillez donc mesurer la porté de vos requêtes et bien les vérifier avant de les soumettre!</span>
                        </div>
                </div>
            </div>
        </div>
    </form>
    @endif
</x-z-modal-generator>