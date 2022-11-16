<x-z-modal-generator :topPosition="300" :hasHeader="true" :modalHeaderTitle="'Gestionnaire des coéfiscients'" :width="6" :icon="'fa bi-calculator'" :modalName="'editClasseGroupCoeficientsModal'" :modalBodyTitle="'Edition des coéfiscients'">
    @if($classe_group && $subjects && count($subjects) > 0)
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="col-12 d-flex justify-content-between row m-0 p-0">
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez la matière </label>
                            <select class="px-2 form-select text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model.defer="subject_id" name="subject_id">
                                <option disabled class="" value="{{null}}">Choisissez la matière</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{$subject->id}}">{{$subject->name}}</option>
                                @endforeach
                            </select>
                            @error('subject_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="col-7 m-0 p-0 ">
                            <x-z-input :placeholder="'Veuillez renseigner la valeur du coéfiscient'" :type="'text'" :error="$errors->first('value')" :modelName="'value'" :labelTitle="'La valeur du coéfiscient...'" ></x-z-input>
                        </div>
                    </div>
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Insérer</x-z-button>
        </div>
    </form>
    @endif
</x-z-modal-generator>