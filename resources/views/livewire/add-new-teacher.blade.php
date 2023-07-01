<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa fa-user-plus'" :modalName="'addNewTeacherModal'" :modalBodyTitle="$title">
    <form x-data={} autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="insert">
        @if($creating || $updating)
            <div class="row justify-between">
                <div class="mt-0 mb-2 col-11 mx-auto">
                    <div class="d-flex row justify-between m-0 p-0">
                        <div class="col-12 m-0 p-0">
                            <div class="col-12 m-0 p-0 d-flex justify-content-between">
                                <div class="col-7 m-0 p-0">
                                    <div class="p-0 m-0 mt-0 mb-2 row">
                                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Nom</label>
                                        <input autofocus="autofocus" placeholder="Nom de l'enseignant" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('name') text-danger border-danger @enderror" wire:model.defer="name" type="text" name="name">
                                        @error('name')
                                            <small class="py-1 z-text-orange">{{$message}}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-4 m-0 p-0">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Statut marital </label>
                                    <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('marital_status') text-danger border border-danger @enderror" wire:model.defer="marital_status" name="marital_status">
                                        <option disabled class="" value="{{null}}">Précisez la situation matrimoniale</option>
                                        <option  value="single">Célibataire</option>
                                        <option  value="maried">Marié</option>
                                        <option  value="mariedonly">Marié sans enfants</option>
                                        <option  value="divorced">Divorcé</option>
                                        <option  value="other">Autre</option>
                                    </select>
                                    @error('marital_status')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 m-0 p-0 d-flex justify-content-between">
                                <div class="p-0 m-0 mt-0 mb-2 row col-7">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Les prénoms</label>
                                    <input autofocus="autofocus" placeholder="Prénoms de l'enseignant" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('surname') text-danger border-danger @enderror" wire:model.defer="surname" type="text" name="surname">
                                    @error('surname')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>

                                <div class="col-4 m-0 p-0">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">L'année scolaire </label>
                                    <select @if($teacher) disabled @endif class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('school_year') text-danger border border-danger @enderror" wire:model.defer="school_year" name="school_year">
                                        <option class="" value="{{null}}">Choisissez une année</option>
                                        @foreach ($school_years as $school_year)
                                            <option  value="{{$school_year->id}}">{{$school_year->school_year}}</option>
                                        @endforeach
                                    </select>
                                    @error('school_year')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-12 d-flex justify-content-between m-0 p-0">
                            <div class="col-5 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le cycle </label>
                                <select @if($teacher) disabled @endif class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('level_id') text-danger border border-danger @enderror" wire:model.defer="level_id">
                                    <option class="" value="{{null}}">Choisissez le cycle</option>
                                    @foreach ($levels as $l)
                                        <option  value="{{$l->id}}">{{$l->getName()}}</option>
                                    @endforeach
                                </select>
                                @error('level_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                            <div class="col-6 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La spécialité </label>
                                <select @if($teacher) disabled @endif class="px-2 custom-select form-selec text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model.defer="subject_id">
                                    <option class="" value="{{null}}">Choisissez la matière/Spécialité</option>
                                    @foreach ($subjects as $sub)
                                        <option  value="{{$sub->id}}">{{$sub->name}}</option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex row justify-between m-0 p-0 mt-1">
                        <div class="col-12 m-0 p-0 d-flex row justify-between">
                            <div class="col-6 m-0 p-0">
                                <div class="p-0 m-0 mt-0 mb-2 row">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Contacts</label>
                                    <input autofocus="autofocus" placeholder="Contacts : 66475898/215784554" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('contacts') text-danger border-danger @enderror" wire:model.defer="contacts" type="text" name="contacts">
                                    @error('contacts')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-5 m-0 p-0">
                                <div class="p-0 m-0 mt-0 mb-2 row">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Nationalité</label>
                                    <input autofocus="autofocus" placeholder="Contacts : 66475898/215784554" class="text-white form-control bg-transparent border border-white px-2 z-focus @error('nationality') text-danger border-danger @enderror" wire:model.defer="nationality" type="text" name="nationality">
                                    @error('nationality')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between col-12 m-0 p-0">
                            <div class="col-7 m-0 p-0">
                                <div class="p-0 m-0 mt-0 mb-2 row">
                                    <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Adresse mail</label>
                                    <input @if($teacher) disabled @endif autofocus="autofocus" placeholder="Renseignez votre compte utilisateur" class="form-control bg-transparent border border-white px-2 z-focus @error('email') text-danger border-danger @enderror @if($user) text-success border-success @else text-white @endif" wire:model="email" type="text" name="email">
                                    @error('email')
                                        <small class="py-1 z-text-orange">{{$message}}</small>
                                    @elseif($user)
                                        <small class="bi-check-all text-success mr-1 mt-1"></small>
                                        <small class="py-1 text-success">Un compte a été trouvé au nom de {{$user->pseudo}}</small>
                                    @else

                                    @enderror
                                </div>
                            </div>

                            <div class="col-4 m-0 p-0">
                                <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Rechercher un compte </label>
                                <select @if($teacher) disabled @endif class="px-2 form-select custom-select text-white z-bg-secondary w-100 @error('account') text-danger border border-danger @enderror" wire:model="account" name="account">
                                    <option disabled class="" value="{{null}}">Choisissez un compte</option>
                                    @foreach ($users as $u)
                                        @if(!$u->teacher && $u->hasVerifiedEmail())
                                            <option   value="{{$u->id}}">{{$u->pseudo . ' (' . $u->email . ')' }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('account')
                                    <small class="py-1 z-text-orange">{{$message}}</small>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @elseif($edit_subject)
            <div class="my-1 p-1 text-center text-white-50">
                <h6 class="mx-auto p-1 text-white-50">
                    <h1 class="m-0 p-0">
                        <span class="bi-exclamation-triangle text-warning text-center p-1"></span>
                    </h1>
                    Vous êtes sur le point de changer la matière ou la spécialité {{ $teacher ?  'de Mr/Mme ' . $teacher->getFormatedName() : " d 'un enseignant!" }}.
                    <span class="text-warning">Cette procédure est irréversible!</span> <br>
                    <span class="text-warning text-uppercase">L'enseignant perdra ses classes inévitablement </span>
                    pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                    <blockquote class="text-info">
                        Vous pouvez continuer si vous êtes de l'action que vous effectuer!
                    </blockquote>
                </h6>
                <hr class="bg-secondary text-secondary m-0 p-0 w-100">
            </div>
            <div class="row justify-content-center p-2">
                <div class="col-11 d-flex justify-content-between m-0 p-0">
                    <div class="col-5 m-0 p-0">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Le cycle </label>
                        <select class="px-2 custom-select form-select text-white z-bg-secondary w-100 @error('level_id') text-danger border border-danger @enderror" wire:model.defer="level_id">
                            <option class="" value="{{null}}">Choisissez le cycle</option>
                            @foreach ($levels as $l)
                                <option  value="{{$l->id}}">{{$l->getName()}}</option>
                            @endforeach
                        </select>
                        @error('level_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="col-6 m-0 p-0">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">La spécialité </label>
                        <select class="px-2 custom-select form-selec text-white z-bg-secondary w-100 @error('subject_id') text-danger border border-danger @enderror" wire:model.defer="subject_id">
                            <option class="" value="{{null}}">Choisissez la matière/Spécialité</option>
                            @foreach ($subjects as $sub)
                                <option  value="{{$sub->id}}">{{$sub->name}}</option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                </div>
            </div>
        @endif
        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Valider</x-z-button>
        </div>
    </form>
</x-z-modal-generator>
    