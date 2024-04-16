<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="$title" :width="8" :icon="'fa fa-book'" :modalName="'updateClassePupilsDataFromFileModal'" :modalBodyTitle="$title">
    @if($classe)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" enctype="multipart/form-data">
        <div class="row justify-between w-100">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Classe (e) : 
                        <span class="text-warning">
                            {{$classe->name}} 
                        </span>
                    </blockquote>
                </div>
               
            </div>
        </div>

        <div class="m-0 p-0 w-100 bg-transparent py-2 p-3 mx-auto col-11 row border border-dark border-0-cyan border-1-cyan d-flex justify-content-center">
            <div class=" col-12 mx-auto border">
                <div class="m-0 p-0 w-100 my-2 p-2">
                        <div>
                            <label class="text-orange" for="data_file">Selectionner votre fichier</label>
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
                </div>
            </div>
        </div>
    </form>
    @endif
</x-z-modal-generator>