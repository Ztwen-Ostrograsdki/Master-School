<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="'Création de nouvel cycle'" :width="6" :icon="'fa fa-bookmark'" :modalName="'createNewLevel'" :modalBodyTitle="'Création de cycle'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="col-12 row m-0 p-0">
                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le cycle </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('name') text-danger border border-danger @enderror" wire:model.defer="name" name="name">
                                <option disabled class="" value="{{null}}">Choisissez le cycle</option>
                                <option  value="maternal">La Maternelle</option>
                                <option  value="primary">Le Primaire</option>
                                <option  value="secondary">Le Secondaire</option>
                                <option  value="superior">Le Supérieure</option>
                            </select>
                            @error('name')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-7 m-0 p-0 ">
                            <x-z-input :type="'text'" :error="$errors->first('other_level')" :modelName="'other_level'" :labelTitle="'Un autre cycle'" ></x-z-input>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez si vous voulez lier ce cycle aux années scolaires antérieures </label>
                        <select class="px-2 form-select text-white z-bg-secondary w-100 @error('joined') text-danger border border-danger @enderror" wire:model.defer="joined" name="joined">
                            <option disabled class="" value="{{null}}">Choisissez si vous voulez lier ce cycle aux années scolaires antérieures</option>
                            <option  value="true">Oui</option>
                            <option  value="false">Non</option>
                        </select>
                        @error('joined')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Insérer</x-z-button>
        </div>
    </form>
</x-z-modal-generator>