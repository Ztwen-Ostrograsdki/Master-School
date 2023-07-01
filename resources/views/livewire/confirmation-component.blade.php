<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'CONFIRMATION DE REQUETE'" :width="6" :icon="'fa fa-check'" :modalName="'confirmatorModal'" :modalBodyTitle="$title">
    <div class="p-0 m-0 mx-auto col-11 py-2">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex justify-content-center w-100">
                     <div class="w-100 d-flex justify-content-center">
                        <div class=" row w-100 p-0 m-0">
                            <div class="bg-transparent p-0 py-2 col-12">
                                <h6 class="text-warning text-center mx-auto p-1 m-0 py-1">Préciser l'action à effectuer</h6>
                                @csrf
                                <div class="d-flex justify-between mx-auto w-100 p-2">
                                    <form autocomplete="false" method="post" class="mt-2 form-group bg-transparent col-5" wire:submit.prevent="fullDelete">
                                        <div class="p-0 m-0 mx-auto d-flex w-100 justify-content-center pb-1 pt-1">
                                            <button class="w-100 border border-white btn btn--pill bg-danger" type="submit">Suppression avancée</button>
                                        </div>
                                    </form>
                                    <form autocomplete="false" method="post" class="mt-3 col-5" wire:submit.prevent="onceDelete">
                                        <div class="p-0 m-0 mx-auto w-100 d-flex justify-content-center pb-1 pt-1">
                                            <button class="w-100 border border-white btn btn--pill bg-success" type="submit">Suppression statique</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <span wire:click="cancel" class="btn btn-primary col-6 p-2">Abandonner la requête</span>
        </div>
    </div>
</x-z-modal-generator>