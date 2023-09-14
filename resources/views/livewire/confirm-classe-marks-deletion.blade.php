<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa fa-check'" :modalName="'confirmClasseMarksDeletionModal'" :modalBodyTitle="$title">
    <div class="p-0 m-0 mx-auto col-11 py-2">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex justify-content-center w-100">
                     <div class="w-100 d-flex justify-content-center">
                        <div class=" row w-100 p-0 m-0">
                            <div class="bg-transparent p-0 py-2 col-12">
                                @if($classe)
                                    <h6 class="text-warning text-center mx-auto p-1 m-0 py-1"> 
                                        Vous êtes sur le point de supprimer des notes de la classe de la de classe de  {{$classe->name}} 
                                        @if($pupil) de l'apprenant <span class="text-orange">{{$pupil->getName()}}</span> @endif
                                    </h6>
                                    <h6 class="letter-spacing-12 my-2 font-italic text-orange text-center mx-auto">Cette suppression est irreversible et les notes ne pourront être restaurées après suppression!</h6>
                                @endif
                                <div class="d-flex justify-between mx-auto w-100 p-2">
                                    <div class="p-0 m-0 mx-auto d-flex col-5 justify-content-center pb-1 pt-1">
                                        <button wire:click="confirmed" class="w-100 border border-white btn btn--pill bg-danger z-scale" type="submit">Confirmer suppression</button>
                                    </div>
                                    <div class="p-0 m-0 mx-auto col-5 d-flex justify-content-center pb-1 pt-1">
                                        <button wire:click="cancel" class="w-100 border z-scale border-white btn btn--pill bg-secondary" type="submit">Annuler le processus</button>
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