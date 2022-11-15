<x-z-modal-generator :hasHeader="false" :width="6" :icon="'fa fa-filter'" :modalName="'classeGroupManageModal'" :modalBodyTitle="'Edition des groupes pédagogiques de promotion '">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">

        <div class="row justify-between">
            <div class="mt-0 mb-2 col-7">
                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Les groupes pédagogiques</label>
                <select multiple class="px-2 form-select text-white z-bg-secondary w-100 @error('classes') text-danger border border-danger @enderror" wire:model.defer="classes" name="classes">
                    <option disabled class="" value="{{null}}">Choisissez les groupes</option>
                    @foreach ($groupes_pedagogiques as $classe)
                        @if(!in_array($classe->id, $classes))
                            <option  wire:click="changeClasse({{$classe->id}})" value="{{$classe->id}}">{{$classe->name}}</option>
                        @endif
                    @endforeach
                </select>
                @error('classe_id_for_group')
                    <small class="py-1 z-text-orange">{{$message}}</small>
                @enderror
            </div>
            <div class="mt-0 mb-2 col-4">
                <h5 class="mt-5 text-center text-warning">
                   {{count($classes)}} Classes sélectionnées!
                </h5>
            </div>
        </div>
        <div class="d-flex justify-content-center border p-2 rounded">
            @foreach ($classes as $classe_id)
                <span class="border border-secondary rounded px-2 mx-1">
                    <small class="mx-1">{{ $classes_tabs[$classe_id] }}</small>
                    <b wire:click="removeClasse({{$classe_id}})" class="fa bi-trash text-orange cursor-pointer"></b>
                </span>
            @endforeach
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button>Valider</x-z-button>
        </div>
    </form>
</x-z-modal-generator>