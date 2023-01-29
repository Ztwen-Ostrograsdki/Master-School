<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="'Reglages'" :width="6" :icon="'fa fa-bookmark'" :modalName="'marksSettingsModal'" :modalBodyTitle="'Actions sur les notes'">
    @if(true)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submitMarks">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex col-12 m-0 p-0 mx-auto justify-center">
                    <blockquote class="text-info w-100 m-0 my-2">
                        <span class="fa bi-person-check"></span>
                        Apprenant (e) : 
                        <span class="text-warning">
                            Nom balala 
                        </span>
                        en <span class="text-white">matière</span>
                    </blockquote>
                </div>
               <div class="d-flex row">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <span class="cursor-pointer btn btn-primary w-100 border py-2 text-center">
                            <span class="bi-eye mx-2"></span>
                            <span >Ne plus oublier</span>
                        </span>
                        <span class="cursor-pointer btn btn-info w-100 border py-2 text-center">
                            <span class="bi-eye-slash mx-2"></span>
                            <span >Oublier cette note</span>
                        </span>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Terminer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>