<div class="m-0 p-0 w-100" >  
    @if($has_school) 
        @if($has_data)
            @livewire('admin-container-component')
        @else
            <div class="container-fluid">
                <div class="mx-auto d-flex flex-column justify-content-center">
                    <h5 class="h2 text-success mx-auto text-center">
                        Bravo!!!
                        Votre école a été créé! <br>
                    </h5>
                    <small class="text-white-50 text-center">Continuer la personalisation de votre école selon votre goût et vos conditions internes!</small>
                </div>
                <hr class="bg-secondary my-1 p-0 w-100">
                <div class="p-0 px-2 mx-auto justify-between row w-75 my-2">
                    <span wire:click="setActiveSection" class="btn btn-primary border border-white col-5">Tableau de bord standard</span>
                    <span wire:click="setActiveSection({{1}})" class="btn btn-primary border border-white col-5">Tableau de bord interractif</span>
                </div>
                <hr class="bg-secondary my-1 p-0 w-100">
                @if($active_section == 'standard_section')
                <div class="w-100">
                    <blockquote>
                        <div class="d-flex justify-between m-0 px-2">
                            <h5 class="text-white h6">
                                Bienvenue sur la page d'administration de votre nouvelle école. <br>
                                Vous avez créé déjà votre école. <br>
                                <span class="text-warning">
                                Veuillez à présent exécuter l'une des requêtes suivantes par click sur l'un des boutons pour finaliser le processus! </span>
                            </h5>
                            <h5 class="h4 text-info mx-auto text-center mt-2">
                                {{ $progress}} % <span> effectués</span>
                            </h5>
                        </div>
                    </blockquote>
                </div>
                <div class="w-100 p-2 mx-auto d-flex row">
                    <div class="d-flex justify-content-between col-12 p-0 m-0">
                        <span wire:click="generateSchoolYear({{-1}})" class="btn btn-info text-left p-2 my-1 col-5 border border-white">
                            <span class="mx-3">Générer l'année-scolaire basse</span>
                            <span class="bi-calendar"></span>
                            <span class="fa fa-arrow-left float-left mt-1"></span>
                        </span>
                        <span wire:click="generateSchoolYear({{1}})" class="btn btn-success text-left p-2 my-1 col-5 border border-white">
                            <span class="bi-calendar"></span>
                            <span class="mx-3">Générer l'année-scolaire suivante</span>
                            <span class="fa fa-arrow-right float-right mt-1"></span>
                        </span>
                    </div>
                    <span wire:click="definedSemestrePeriod" class="btn btn-primary text-left p-2 my-1 col-12 border border-white">
                        <span class="bi-calendar"></span>
                        <span>Définir la période des semestres/trimestres</span>
                    </span>
                    <span wire:click="createNewLevel" class="btn btn-success text-left p-2 my-1 col-12 border border-white">
                        <span class="bi-house"></span>
                        <span>Créer un cycle</span>
                    </span>
                    <span wire:click="createNewClasse" class="btn bg-orange text-left p-2 my-1 col-12 border border-white">
                        <span class="bi-house"></span>
                        <span>Créer une classe</span>
                    </span>
                    <span wire:click="addNewPupilToClasse" class="btn btn-primary text-left p-2 my-1 col-12 border border-white">
                        <span class="bi-person-plus"></span>
                        <span>Inséser un élève</span>
                    </span>
                    <span class="btn btn-secondary text-left p-2 my-1 col-12 border border-white">
                        <span class="bi-person-badge"></span>
                        <span>Inséser un personnel</span>
                    </span>
                    <span class="btn btn-info text-left p-2 my-1 col-12 border border-white">
                        <span class="bi-person-plus"></span>
                        <span>Insérer un enseignant</span>
                    </span>
                    <span wire:click="createNewSubject" class="btn btn-warning text-left p-2 my-1 col-12 border border-white">
                        <span class="fa fa-bookmark"></span>
                        <span>Créer une matière</span>
                    </span>
                    <span wire:click="addNewClasseGroup" class="btn btn-success text-left p-2 my-1 col-12 border border-white">
                        <span class="fa fa-filter"></span>
                        <span>Créer une promotion de classes</span>
                    </span>
                    <span wire:click="reloadClassesPromotionAndPosition" class="btn btn-primary text-left p-2 my-1 col-12 border border-white">
                        <span class="fa fa-recycle"></span>
                        <span>Recharger les promotions et position de chacune des classes déjà créées</span>
                    </span>
                </div>
                @else



                @endif
            </div>
        @endif
    @else
        @if(!$start_new_school)
            <div class="container-fluid">
                <div class="mx-auto d-flex justify-content-center">
                    <h5 class="fa fa-2x">Bienvenue !</h5>
                </div>
                <div class="w-100">
                    <blockquote>
                        <h5 class="text-white h6">
                            Bienvenue sur la page d'administration de votre nouvelle école. <br>
                            Veuillez cliquer sur bouton pour créer votre école!
                        </h5>
                    </blockquote>
                </div>
                <div class="w-100 p-2 mx-auto d-flex justify-content-center">
                    <span wire:click="throwSchoolBuiding" class="btn btn-primary p-2 w-75 border border-white">Créer mon école</span>
                </div>
            </div>
        @else
            <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="buildSchool">
                {{ csrf_field()}}
                <div class="row mx-auto w-100 ">
                    <div class="col-12 col-xl-5 col-lg-5 col-xlg-5 mt-0 mb-2">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="school_name">Le nom de votre école</label>
                        <input placeholder="Veuillez renseigner le nom de votre école..." class="text-white form-control bg-transparent border border-white px-2" wire:model.defer="school_name" type="text" name="school_name" id="school_name">
                        @error('school_name')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="mt-0 mb-2 col-12 col-xl-3 col-lg-3 col-xlg-3">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="classe_subjects">Le format: Trimestre ou semestre ?</label>
                        <select class="px-2 form-select text-white w-100" wire:model.defer="semestre_type" name="semestre_type">
                            <option disabled class="" value="{{null}}">Choisissez : trimestres ou semestres</option>
                            <option class="" value="trimestre">Trimestre</option>
                            <option class="" value="semestre">Semestre</option>
                        </select>
                        @error('semestre_type')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    <div class="mt-0 mb-2 col-12 col-xl-3 col-lg-3 col-xlg-3">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer">Choisissez l'année-scolaire initiale</label>
                        <select class="px-2 form-select text-white w-100" wire:model.defer="school_year_start" name="school_year_start">
                            <option disabled class="" value="{{null}}">Choisissez une année scolaire</option>
                            @foreach ($school_years_tabs as $school_year)
                                <option  value="{{$school_year}}">{{$school_year}}</option>
                            @endforeach
                        </select>
                        @error('school_year_start')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                </div>
                <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
                    <x-z-button>Valider</x-z-button>
                </div>
            </form>
        @endif
    @endif
</div>
