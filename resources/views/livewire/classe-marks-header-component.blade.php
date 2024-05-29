<div>
    <div class="w-100 m-0 p-0 mb-4">
        @if(!$teacher_profil)
            <select wire:model="classe_subject_selected" class="form-select custom-select w-auto d-block ">
                <option value="{{null}}">Veuillez sélectionner une matière</option>
                @foreach ($classe_subjects as $s)
                    <option value="{{$s->id}}">{{$s->name}}</option>
                @endforeach
            </select>
        @endif
        @if($subject_selected)
            
            @if($classe && $pupils && $marks)
                <span class="text-dark float-right btn btn-secondary border mx-1">
                    @if(!$computedRank)
                        <span wire:click="displayRank" title="Afficher les rangs" class="d-inline-block z-scale w-100 cursor-pointer">
                            <small>Le rang</small>
                            <span class="bi-eye text-dark"></span>

                        </span>
                    @else
                        <span wire:click="hideRank" title="Masquer les rangs" class="d-inline-block z-scale w-100 cursor-pointer">
                            <small>Masquer rang</small>
                            <span class="bi-eye-slash  text-black-50"></span>
                        </span>
                    @endif
                </span>
            @endif
            @if($classe && $classe->classeWasNotClosedForTeacher(auth()->user()->teacher->id) && $classe->classeWasNotLockedForTeacher(auth()->user()->teacher->id))
                @if($classe && $subject_selected)
                    <span class="float-right btn btn-warning border ml-1">
                        @if($classe->hasSubjectsSanctions(session('semestre_selected'), $subject_selected->id, $school_year_model->id , true))
                            <span wire:click="desactivated({{$classe->id}})" title="Ne pas prendre en compte les sanctions" class="d-inline-block w-100 cursor-pointer z-scale">
                                <small>Pas tenir</small>
                                <span class="bi-pin-angle"></span>
                            </span>
                        @else
                            <span wire:click="activated({{$classe->id}})" title="Prendre en compte les sanctions" class="d-inline-block w-100 cursor-pointer z-scale">
                                <small>Tenir compte</small>
                                <span class="bi-pin-angle"></span>
                            </span>
                        @endif
                    </span>
                @endif

                @if($hasModalities)
                    <span class="text-warning float-right btn btn-secondary border">
                        @if($modalitiesActivated)
                            <span wire:click="diseableModalities" title="Désactiver tamporairement les modalités" class="d-inline-block w-100 cursor-pointer z-scale">
                                <small>Désactiver</small>
                                <span class="bi-key text-warning"></span>

                            </span>
                        @else
                            <span wire:click="activateModalities" title="Réactiver les modalités" class="d-inline-block w-100 cursor-pointer z-scale">
                                <small>Activer</small>
                                <span class="bi-unlock text-success"></span>
                            </span>
                        @endif
                    </span>
                @endif

                @if($classe && $subject_selected)
                    <span wire:click="manageModality" class="btn btn-primary z-scale border border-white float-right mr-1" title="Editer les modalités de calcule de moyenne dans la matière sélectionnée dans cette classe">
                        <span class="fa bi-pen"></span>
                        <span class="fa bi-calculator"></span>
                    </span>

                    <span wire:click="updateParticipatesClasseMarks" class="btn btn-warning z-scale border border-white float-right mr-1" title="Mettre à jour les notes de participations de la matière sélectionnée dans cette classe">
                        <span class="fa bi-wrench-adjustable"></span>
                        <span class="fa bi-wrench-adjustable-circle"></span>
                    </span>
                @endif
            @endif
        @endif
        @if(auth()->user()->isAdminAs('master'))
            @if(!$teacher_profil && $classe)
                <span wire:click="editClasseSubjects({{$classe->id}})" class="btn btn-success border border-white float-right mr-1 z-scale" title="Ajouter une matière à cette classe">
                    <span class="fa bi-file-earmark-diff"></span>
                </span>
            @endif
        @endif
        @if($classe)
            @if($marks)
                <span wire:click="refreshClasseMarks('{{$classe->id}}')" class="btn btn-danger border z-scale border-white mx-1 float-right" title="Vider des notes de cette classe">
                    <span class="fa fa-trash"></span>
                </span>
            @endif
            <span wire:click.prefetch="insertClasseMarks" class="btn btn-primary border z-scale border-white mr-1 float-right" title="Insérer des notes de classe">
                <span class="fa fa-upload"></span>
                <small>Insérer</small>
            </span>
            <span wire:click="convertClasseLastMarksToParticipateMarks" class="btn btn-warning border z-scale border-white mr-1 float-right" title="Convertir des notes de classe: c'est-à-dire modifier le type de certaines notes de la classe">
                <span class="fa fa-recycle"></span>
                <small>Convertir</small>
            </span>
            <span wire:click="restorMarks({{$classe->id}})" class="btn btn-secondary border z-scale border-white mr-1 float-right" title="Restaurer des notes de classe">
                <span class="fa fa-reply"></span>
            </span>

            <span wire:click="showFormattedView({{$classe->id}})" class="btn btn-primary mx-2 border z-scale border-white mr-1 float-right" title="@if($simpleFormat) Masquer @else Afficher @endif le format simplifié des notes de classe">
                <span class="fa bi-binoculars"></span>
                <small> Vue @if($simpleFormat) globale @else simple @endif</small>
            </span>

            <span wire:click="updateClassePupilsPersoDataFromFile({{$classe->id}})" class="btn btn-warning border z-scale border-white mr-1 float-right" title="Mettre à jour les données des apprenants à partir d'un fichier">
                <span class="fa fa-download"></span>
            </span>

            <span wire:click="updateClasseMarksToSimpleExcelFile({{$classe->id}})" class="btn btn-success border z-scale border-white mr-1 float-right" title="Mettre à jour le fichier de notes des apprenants">
                <span class="fa fa-upload"></span>
            </span>

            <span wire:click="showMarksInsertionProgress" class="btn btn-success border z-scale border-white mr-1 float-right" title="Afficher la progression des notes en cours">
                <span class="fa fa-recycle"></span>
            </span>
        @endif


    </div>

    <div class="">
        <blockquote class="text-primary w-100 m-0" style="margin-top: 50px !important;">
            <h5 class="m-0 p-0 text-white-50 h6 w-100 d-flex justify-content-between flex-column">
                <span class="d-flex justify-content-between">
                    <span>Les détails sur les notes</span>
                    <span class="d-flex justify-content-between">
                        <span class="text-warning mx-2">
                            @if($classe && $classe->position)

                                <span class="mr-2">IDX : </span>
                                <span>{{ $classe->position }}</span>

                            @else
                                <small class="text-warning">L'IDX de cette classe n'a pas été définie</small>
                            @endif
                            <b class="text-white-50"> - </b>
                        </span>

                        <span class="text-orange mx-2">
                            @if($classe && $classe->filial)

                                <span class="mr-2">Filière : </span>
                                <span>{{ $classe->filial->name }}</span>

                            @else
                                <small class="text-warning">La filière de cette classe n'a pas été définie</small>
                            @endif
                            <b class="text-white-50"> - </b>
                        </span>

                        @if(!$teacher_profil)
                            @if($classe && $classe->classe_group)
                                <a title="charger le profil de la promotion" class="text-success mx-1" href="{{route('classe_group_profil', ['slug' => $classe->classe_group->name])}}">
                                    Promotion {{ $classe->classe_group->name }}
                                </a>
                            @else
                                @if(auth()->user()->isAdminAs('master'))
                                    <span wire:click="editClasseGroup({{$classe->id}})" title="Cette classe n'est pas encore liée à une promotion, veuillez cliquer afin de le faire et d'avoir accès aux coéfiscients des différentes matières" class="mx-1 p-0 px-2 btn btn-success border border-white">
                                        Promouvoir maintenant
                                    </span>
                                @endif
                            @endif
                            <span class="ml-3">Coef:  {{ $classe_subject_coef }}</span>
                        @endif
                    </span>
                </span>
                <span class="mx-2">
                    @if($current_period)
                        <span>
                            <small class="text-white-50">Nous sommes dans le {{ $current_period['target'] }}</small>
                            <small class="text-success">Il y a déjà {{ $current_period['passed'] }} qui se sont écoulées</small>
                            <small class="text-danger">Il nous reste encore {{ $current_period['rest'] }}</small>
                            <span class="mx-2 text-white-50">
                                <small class="fa fa-warning text-danger"></small>
                                <small class="fa fa-warning text-danger"></small>
                                <small>Après cette période, l'insertion des notes sera bloquée pour ce semestre!</small>
                                <small class="fa fa-warning text-danger"></small>
                            </span>
                        </span>
                    @endif
                </span>
            </h5>
        </blockquote>
        <h6 class="m-0 mx-auto text-right p-1 text-danger bg-orange">
            @if($classe && session()->has('classe_subject_selected') && session('classe_subject_selected') && $classe->hasNullsMarks(session('semestre_selected'), null, session('classe_subject_selected')))
                <span class="bi-exclamation-triangle text-warning"></span>
                <small class="mr-1 letter-spacing-12 cursive text-cursive fx-18 text-white-50">
                    Plusieurs apprenants de cette classe ont eu la note <b class="text-warning">00 / 20 </b>!
                </small>
            @endif
        </h6>
        <hr class="bg-warning w-100 p-0 p-0">
        @if($subject_selected && isset($subject_selected->name))
            <small class="text-warning m-2">
                @if($modality && $modalitiesActivated)
                    <small class="bi-calculator mr-1"></small>Pour le calcule des moyennes d'interros de <b class="text-white">{{$subject_selected->name}}</b>, <b class="text-white">0{{$modality}}</b> notes plus la note de participation seront prises en comptes!
                @else
                    <small class="bi-calculator mr-1"></small>Pour le calcule des moyennes d'interros de  <b class="text-white">{{$subject_selected->name}}</b>, toutes les notes plus la note de participation seront prises en comptes!
                @endif
            </small>
            @if($modality && $modalitiesActivated)
                <br>
                <small class="mr-3 text-warning">Les notes surlignées en vert sont celles prises en compte ou celles considées comme étant @if(isset($modality) && $modality && $modalitiesActivated) les <b class="text-white">0{{$modality}}</b> @endif meilleures notes!</small>
            @endif
        @endif
        <hr class="bg-warning w-100 p-0 p-0">
    </div>
</div>
