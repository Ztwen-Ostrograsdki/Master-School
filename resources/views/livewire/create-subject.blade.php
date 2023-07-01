<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa fa-bookmark'" :modalName="'createNewSubject'" :modalBodyTitle="$title">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-11 mx-auto">
               <div class="d-flex row">
                    <div class="m-0 p-0 col-12">
                        <label class="z-text-cyan m-0  p-0 w-100 cursor-pointer">Le nom de la matière</label>
                        <input placeholder="Veuillez renseigner le nom de la matière..." class="text-white form-control bg-transparent border border-white px-2 @error('name') text-danger border-danger @enderror" wire:model.defer="name" type="text" name="name">
                        @error('name')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="col-12 row m-0 p-0 d-flex justify-content-between">
                        <div class="col-4 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez le cycle </label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('level_id') text-danger border border-danger @enderror" wire:model.defer="level_id" name="level_id">
                                <option class="" value="{{null}}">Choisissez le cycle</option>
                                @foreach($levels as $level)
                                    <option  value="{{$level->id}}"> {{ $level->getName()}} </option>
                                @endforeach
                            </select>
                            @error('level_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        @if($updating)
                        <div class="col-5 m-0 p-0">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'AE </label>
                            <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('teacher_id') text-danger border border-danger @enderror" wire:model.defer="teacher_id" name="teacher_id">
                                <option class="" value="{{null}}">Choisissez l'AE</option>
                                @foreach($teachers as $teacher)
                                    <option  value="{{$teacher->id}}"> {{ $teacher->getFormatedName()}} </option>
                                @endforeach
                            </select>
                            @error('teacher_id')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                        @endif
                    </div>
                    @if(!$updating)
                    <div class="col-12">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez si vous voulez lier cette matière aux années scolaires antérieures </label>
                        <select class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('joined') text-danger border border-danger @enderror" wire:model.defer="joined" name="joined">
                            <option disabled class="" value="{{null}}">Choisissez si vous voulez lier cette matière aux années scolaires antérieures</option>
                            <option  value="true">Oui</option>
                            <option  value="false">Non</option>
                        </select>
                        @error('joined')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    @endif
               </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Insérer</x-z-button>
        </div>
    </form>
</x-z-modal-generator>