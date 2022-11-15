<x-z-modal-generator :hasHeader="false" :width="6" :icon="'fa fa-filter'" :modalName="'editClasseGroupModal'" :modalBodyTitle="'Edition de la promotion de classe'">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-content-center mx-auto">
            <div class="mt-0 mb-2 col-9">
                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la promotion à laquelle vous voulez ralier cette classe </label>
                <select class="px-2 form-select text-white z-bg-secondary w-100 @error('classe_group_id') text-danger border border-danger @enderror" wire:model.defer="classe_group_id" name="classe_group_id">
                    <option disabled class="" value="{{null}}">Choisissez la promotion</option>
                    <option class="" value="{{null}}">Retirer de la promotion en cours</option>
                    @foreach ($promotions as $promotion)
                        <option value="{{$promotion->id}}">{{$promotion->name}}</option>
                    @endforeach
                </select>
                @error('classe_group_id')
                    <small class="py-1 z-text-orange">{{$message}}</small>
                @enderror
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button>Valider</x-z-button>
        </div>
    </form>
</x-z-modal-generator>