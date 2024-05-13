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
                
                @if(!$to_confirm)
                <div class="d-flex row justify-between">

                    <div class="col-7 m-0 p-0">
                        <div class="p-0 m-0 mt-0 mb-2 row">
                            <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Matricule de l'apprenant</label>
                            <input autofocus="autofocus" placeholder="Le matricule de l'apprenant ou son numero educMaster" class="text-white form-control bg-transparent border border-white px-2 mt-1 z-focus @error('matricule') text-danger border-danger @enderror" wire:model.defer="matricule" type="text" name="matricule">
                            @error('matricule')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="col-4">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le lien parentale </label>
                        <select class="px-2 form-select text-white z-bg-secondary mt-1 w-100 @error('lien') text-danger border border-danger @enderror" wire:model.defer="lien">
                            <option class="" value="{{null}}">Choisissez le lien parental</option>
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
                            <input autofocus="autofocus" placeholder="Votre clé secret" class="text-white form-control bg-transparent border border-white px-2 mt-1 z-focus @error('code') text-danger border-danger @enderror" wire:model.defer="code" type="text" name="code">
                            @error('code')
                                <small class="py-1 z-text-orange">{{$message}}</small>
                            @enderror
                        </div>
                    </div>
                    
                </div>
                @elseif($to_confirm && $target)
                    <div class="justify-content-center d-flex flex-column mx-auto p-2">
                        <div class="col-10">
                            <span class="text-white-50 h6">
                                <span class="m-2 text-white-50">
                                Nous avons trouvé un(e) apprenant(e) correspondant à votre demande!<br>
                                </span>
                                <span class="text-white">
                                    Il s'agit de l'apprenant(e) <span class="text-warning">{{ $target ?  $target->getName() : "Inconnu" }}</span>
                                </span>
                                <span class="text-white">
                                    voudriez-vous confirmer la demande?
                                </span>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <span wire:click="confirm" class="btn btn-success col-5 border z-scale">
                                <span class="bi-person-check fx-18"></span>
                                <span>Confirmer ma demande</span>
                            </span>

                            <span wire:click="to_cancel" class="btn btn-danger col-5 border z-scale">
                                <span class="bi-person-x fx-18"></span>
                                <span>Annuler ma demande</span>
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="p-0 m-0 mx-auto w-100 d-flex justify-content-center pb-1 pt-1">
            <span wire:click="submit" class="btn btn-primary border z-scale p-2 col-6">Valider la demande</span>
        </div>
    </form>
</x-z-modal-generator>
    