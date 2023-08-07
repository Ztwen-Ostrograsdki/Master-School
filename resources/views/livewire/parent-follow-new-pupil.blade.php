<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa fa-user-plus'" :modalName="'ParentFollowPupilModal'" :modalBodyTitle="$title">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">

            @if($parentable)
                <blockquote class="text-info w-100 m-0 my-2">
                    <span class="">
                        <span class="fa fa-user-plus mt-3"></span>
                        Parent : 
                        <span class="text-warning mt-3">
                            {{$parentable->name }} 
                        </span>
                    </span>

                </blockquote>
            @endif
            <div class="mt-0 mb-2 col-11 mx-auto">
                
                <div class="d-flex row justify-between">

                    <div class="col-7 m-0 p-0">
                        <div class="p-0 m-0 mt-0 mb-2 row">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Matricule de l'apprenant</label>
                            <input autofocus="autofocus" placeholder="Le matricule de l'apprenant" class="text-white form-control bg-transparent border border-white px-2 mt-1 z-focus @error('matricule') text-danger border-danger @enderror" wire:model.defer="matricule" type="text" name="matricule">
                            @error('matricule')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-4">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le lien parentale </label>
                        <select class="px-2 form-select text-white z-bg-secondary mt-1 w-100 @error('lien') text-danger border border-danger @enderror" wire:model.defer="lien">
                            <option disabled class="" value="{{null}}">Choisissez le lien parental</option>
                            @foreach ($liens as $l)
                                <option  value="{{$l}}">{{$l}}</option>
                            @endforeach
                        </select>
                        @error('lien')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>

                    <div class="col-10 m-0 p-0">
                        <div class="p-0 m-0 mt-0 mb-2 row">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Votre clé secret</label>
                            <input autofocus="autofocus" placeholder="Votre clé secret" class="text-white form-control bg-transparent border border-white px-2 mt-1 z-focus @error('auth_key') text-danger border-danger @enderror" wire:model.defer="auth_key" type="text" name="auth_key">
                            @error('auth_key')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider</x-z-button>
        </div>
    </form>
</x-z-modal-generator>
    