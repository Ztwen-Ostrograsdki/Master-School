<x-z-modal-generator :hasHeader="false" :width="6" :icon="'fa fa-user-plus'" :modalName="'classeSubjectManageModal'" :modalBodyTitle="'Edition des matières de classe'">
<form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">

    <div class="row justify-between">
        <div class="mt-0 mb-2 col-7">
            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="classe_subjects">Les matières</label>
            <select multiple class="px-2 form-select text-white z-bg-secondary w-100 @error('classe_subjects') text-danger border border-danger @enderror" wire:model.defer="classe_subjects" name="classe_subjects" id="classe_subjects">
                <option disabled class="" value="{{null}}">Choisissez les matières</option>
                @foreach ($subjects as $subject)
                    <option  wire:click="changeSubjects({{$subject->id}})" value="{{$subject->id}}">{{$subject->name}}</option>
                @endforeach
            </select>
            @error('classe_subjects')
                <small class="py-1 z-text-orange">{{$message}}</small>
            @enderror
        </div>
        <div class="mt-0 mb-2 col-4">
            <h5 class="mt-5 text-center text-warning">
               {{count($classe_subjects)}} matières sélectionnées!
            </h5>
        </div>
    </div>
    <div class="d-flex justify-content-center border p-2 rounded">
        @foreach ($classe_subjects as $sub)
            <span class="border border-secondary rounded px-2 mx-1">
                <small class="mx-1">{{ mb_substr($classe_subjects_tabs[$sub], 0, 5) }}</small>
                <b wire:click="removeSubject({{$sub}})" class="fa bi-trash text-orange cursor-pointer"></b>
            </span>
        @endforeach
    </div>
    <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
        <x-z-button>Valider</x-z-button>
    </div>
</form>
</x-z-modal-generator>
