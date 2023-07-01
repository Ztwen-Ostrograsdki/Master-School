<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa bi-person-check'" :modalName="'manageClasseReferees'" :modalBodyTitle="$title">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
                <div class="d-flex row justify-content-between m-0 p-0">
                    <div class="col-8">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le PP </label>
                        <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('teacher_id') text-danger border border-danger @enderror" wire:model="teacher_id" name="teacher_id">
                            <option class="" value="{{null}}">Choisissez le PP</option>
                            @foreach ($teachers as $teacher)
                                <option  value="{{$teacher->id}}">{{$teacher->getFormatedName()}}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>


                    <div class="col-3">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">L'année scolaire </label>
                        <select disabled class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('school_year_id') text-danger border border-danger @enderror" wire:model="school_year_id" name="school_year_id">
                            <option class="" value="{{null}}">Choisissez l'année scolaire</option>
                            @foreach ($school_years as $school_year)
                                <option  value="{{$school_year->id}}">{{$school_year->school_year}}</option>
                            @endforeach
                        </select>
                        @error('school_year_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                </div>


                <div class="d-flex row justify-content-between m-0 p-0">
                    <div class="col-5">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le premier respo</label>
                        <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('respo1_id') text-danger border border-danger @enderror" wire:model="respo1_id" name="respo1_id">
                            <option class="" value="{{null}}">Choisissez le premier respo</option>
                            @foreach ($pupils as $p1)
                                <option  value="{{$p1->id}}">{{$p1->getName()}}</option>
                            @endforeach
                        </select>
                        @error('respo1_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="col-5">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le 2nd respo</label>
                        <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('respo2_id') text-danger border border-danger @enderror" wire:model="respo2_id" name="respo2_id">
                            <option class="" value="{{null}}">Choisissez le 2nd respo</option>
                            @foreach ($pupils as $p2)
                                <option  value="{{$p2->id}}">{{$p2->getName()}}</option>
                            @endforeach
                        </select>
                        @error('respo2_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                </div>
               
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Mettre à jour</x-z-button>
        </div>
    </form>
</x-z-modal-generator>
    