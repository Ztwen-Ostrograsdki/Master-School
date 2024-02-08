<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa fa-recycle'" :modalName="'confirmClasseMarksConvertionModal'" :modalBodyTitle="$title">
    <div class="p-0 m-0 mx-auto col-11 py-2">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex justify-content-center w-100">
                     <div class="w-100 d-flex justify-content-center">
                        <div class=" row w-100 p-0 m-0">
                            <div class="bg-transparent p-0 py-2 col-12">
                                @if($classe)
                                    <h6 class="text-warning text-center mx-auto p-1 m-0 py-1"> 
                                        Vous êtes sur le point de convertir des notes de {{$subject->name}} de la classe de  {{$classe->name}} 

                                        @if($pupil) de l'apprenant <span class="text-orange">{{$pupil->getName()}}</span> @endif
                                    </h6>
                                    <h6 class="letter-spacing-12 my-2 font-italic text-orange text-center mx-auto">Cette action peut être reversible et les notes pourront être retablies dans leur type originel!</h6>
                                @endif
                                <div class="mx-auto text-center col-12">
                                    <hr class="bg-warning w-100">

                                    <div class="col-10 mx-auto">
                                        <label class="small z-text-cyan m-0 p-0 w-100 cursor-pointer text-uppercase">Préciser le type de conversion à effectuer </label>
                                        <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('convertion_type') text-danger border border-danger @enderror" wire:model="convertion_type">
                                            <option value="epe-to-participation" class=""> Convertir les dernières notes d'interrogation en participation </option>
                                            <option value="participation-to-epe" class=""> Convertir les notes de participation en notes d'interrogation </option>
                                        </select>
                                        @error('convertion_type')
                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                        @enderror
                                    </div>
                                    <span class="text-center text-warning py-2 my-2 d-inline-block">
                                        {{ $convertion_type_message }}
                                    </span>
                                    <hr class="bg-warning w-100">

                                </div>
                                <div class="d-flex justify-between mx-auto w-100 p-2">
                                    <div class="p-0 m-0 mx-auto d-flex col-5 justify-content-center pb-1 pt-1">
                                        <button wire:click="confirmed" class="w-100 border border-white btn btn--pill bg-danger z-scale" type="submit">Confirmer conversion</button>
                                    </div>
                                    <div class="p-0 m-0 mx-auto col-5 d-flex justify-content-center pb-1 pt-1">
                                        <button wire:click="cancel" class="w-100 border z-scale border-white btn btn--pill bg-secondary" type="submit">Annuler la conversion</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
</x-z-modal-generator>