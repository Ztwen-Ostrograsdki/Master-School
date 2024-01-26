<div>

    <div class="w-100 my-1">
        @if(!$teacher_profil)
            <select wire:model="classe_subject_selected" class="form-select custom-select w-auto d-block ">
                <option value="{{null}}">Veuillez sélectionner une matière</option>
                @foreach ($classe_subjects as $s)
                    <option value="{{$s->id}}">{{$s->name}}</option>
                @endforeach
            </select>
        @endif
        @if($subject_selected)
            <small class="text-warning m-2">
                @if($modality)
                    <small class="bi-calculator mr-1"></small>Pour le calcule des moyennes d'interros de {{$subject_selected->name}}, <b class="text-white">0{{$modality}}</b> notes seront prises en comptes!
                @else
                    <small class="bi-calculator mr-1"></small>Pour le calcule des moyennes d'interros de {{$subject_selected->name}}, toutes les notes seront prises en comptes!
                @endif

            </small>
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
                    <span class="text-warning float-right btn btn-secondary border ml-1">
                        @if($classe->hasSubjectsSanctions(session('semestre_selected'), $subject_selected->id, $school_year_model->id , true))
                            <span wire:click="desactivated({{$classe->id}})" title="Ne pas prendre en compte les sanctions" class="d-inline-block w-100 cursor-pointer z-scale">
                                <small>Pas tenir</small>
                                <span class="bi-exclamation-triangle text-warning"></span>
                            </span>
                        @else
                            <span wire:click="activated({{$classe->id}})" title="Prendre en compte les sanctions" class="d-inline-block w-100 cursor-pointer z-scale">
                                <small>Tenir compte</small>
                                <span class="bi-exclamation-triangle text-success"></span>
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
                <span wire:click="manageModality" class="btn btn-primary z-scale border border-white float-right mr-1" title="Editer les modalités de calcule de moyenne dans la matière sélectionnée dans cette classe">
                    <span class="fa bi-pen"></span>
                    <span class="fa bi-calculator"></span>
                    <small>Editer</small>
                </span>
            @endif
        @endif
        @if(auth()->user()->isAdminAs('master'))
            @if(!$teacher_profil && $classe)
                <span wire:click="editClasseSubjects({{$classe->id}})" class="btn btn-success border border-white float-right mr-1 z-scale" title="Ajouter une matière à cette classe">
                    <span class="fa fa-bookmark"></span>
                    <small>Ajouter</small>
                </span>
            @endif
        @endif
        @if($classe && $pupils)
            @if($marks)
            <span wire:click="refreshClasseMarks('{{$classe->id}}')" class="btn btn-danger border z-scale border-white mx-1 float-right" title="Vider des notes de cette classe">
                <span class="fa fa-trash"></span>
                <small>Vider</small>
            </span>
            @endif
        <span wire:click="insertClasseMarks" class="btn btn-primary border z-scale border-white mr-3 float-right" title="Insérer des notes de classe">
            <span class="fa fa-upload"></span>
            <small>Insérer</small>
        </span>
        @endif
        <hr class="w-100 bg-warning text-warning p-0 m-0 mt-4">
        <hr class="w-100 bg-warning text-warning p-0 m-0 mt-1">
    </div>
    @if($is_loading)
    <div class="w-100 d-flex justify-content-center flex-column">
        @livewire('loader-component')  
    </div>
    @else
    <div class="my-2">
        @if($pupils && $classe_subject_selected && count($pupils) > 0)
        <div>
            <hr class="w-100 text-white p-0 m-0 my-1">
            <blockquote class="text-primary w-100 m-0">
                <h5 class="m-0 p-0 text-white-50 h6 w-100 d-flex justify-content-between flex-column">
                    <span class="d-flex justify-content-between">
                        <span>Les détails sur les notes</span>
                        <span class="d-flex justify-content-between">
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
                                <small class="text-success">Il y a déjà {{ $current_period['passed'] }} qui se sont écoulés</small>
                                <small class="text-danger">Il nous reste encore {{ $current_period['rest'] }}</small>
                                <span class="mx-2 text-white-50">
                                    <small class="fa fa-warning text-danger"></small>
                                    <small class="fa fa-warning text-danger"></small>
                                    <small>Après cete période, l'insertion des notes séra bloquée pour ce semestre!</small>
                                    <small class="fa fa-warning text-danger"></small>
                                </span>
                            </span>
                        @endif
                    </span>
                </h5>
            </blockquote>
            <h6 class="m-0 mx-auto text-right p-1 text-danger bg-orange">
                @if($classe && session()->has('classe_subject_selected') && session('classe_subject_selected') && $classe->hasNullsMarks(session('semestre_selected'), null, session('classe_subject_selected')))
                    <span class="bi-exclamation-triangle"></span>
                    <small class="text-danger mr-1"></small>Plusieurs apprenants de cette classe ont eu la note <b class="text-warning">00 / 20 </b>!
                @endif
            </h6>
        </div>
        <div class="w-100 m-0 p-0 mt-3">
            <table class="w-100 m-0 p-0 table-striped table-bordered z-table hoverable text-white text-center">
                    <col>
                    <col>
                    <colgroup span="{{$epeMaxLenght}}"></colgroup>
                    <colgroup span="{{$participMaxLenght}}"></colgroup>
                    <colgroup span="{{$devMaxLenght}}"></colgroup>
                    <colgroup span="3"></colgroup>
                    <colgroup span="1"></colgroup>
                    <colgroup span="1"></colgroup>
                    <tr class="text-center">
                        <td rowspan="2">No</td>
                        <td rowspan="2">Les apprenants</td>
                        <th colspan="{{$epeMaxLenght}}" scope="colgroup">Les interrogations</th>
                        <th colspan="{{$participMaxLenght}}" scope="colgroup">Les Participations</th>
                        <th colspan="{{$devMaxLenght}}" scope="colgroup">Les devoirs</th>
                        <th colspan="3" scope="colgroup">Les Moyennes</th>
                        <td rowspan="2">Rang</td>
                        <td rowspan="2">
                            <span class="bi-tools"></span>
                        </td>
                    </tr>
                    <tr class="text-center">
                        @for ($e = 1; $e <= $epeMaxLenght; $e++)
                            <th scope="col" class="epe{{$e}}">EPE {{ $e }}</th>
                        @endfor
                        @for ($p = 1; $p <= $participMaxLenght; $p++)
                            <th scope="col" class="particip{{$p}}">Part. {{ $p }}</th>
                        @endfor
                        @for ($d = 1; $d <= $devMaxLenght; $d++)
                            <th scope="col" class="epe{{$d}}">DEV {{ $d }}</th>
                        @endfor
                        <th scope="col">Moy. Int</th>
                        <th scope="col">Moy</th>
                        <th scope="col">Moy. Coef</th>
                        
                    </tr>
                    @foreach($pupils as $k => $p)
                        <tr class="text-left">
                            <th scope="row" class="text-center">{{ $loop->iteration }}</th>
                            <th class="text-capitalize p-0 m-0 d-flex justify-content-between">
                                @isMaster(auth()->user())
                                <a class="text-white m-0 p-0 py-1" href="{{route('pupil_profil', ['id' => $p->id])}}">
                                    <span class="">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </a>
                                @else
                                <span class="text-white m-0 p-0 py-1">
                                    <span class="">
                                        <span class="mx-2 d-none d-lg-inline d-xl-inline text-small @if($p->sexe == 'female') text-orange  @endif ">
                                            {{$p->getName()}}
                                        </span>
                                    </span>
                                </span>
                                @endisMaster
                                <span class="float-right text-right">
                                    <small class="mx-1">
                                        <small class="text-success">
                                            ({{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'bonus', true) }}) 
                                        </small>
                                         <small class="text-danger">
                                             ({{ $p->getRelatedMarksCounter($classe->id, session('classe_subject_selected'), session('semestre_selected'), session('school_year_selected'), 'minus', true) }})
                                        </small>
                                    </small>
                                </span>
                            </th>
                            @if(isset($marks[$p->id]) && $marks[$p->id])
                                {{-- LES EPE --}}
                                @if($marks[$p->id]['epe'])
                                    @foreach ($marks[$p->id]['epe'] as $m => $epe)
                                        <td title="interrogation No {{$epe->mark_index}}" wire:click="setTargetedMark({{$epe->id}})" class="text-center cursor-pointer @if($epe->value == 0) text-warning @endif ">
                                            <span class="w-100 cursor-pointer @if($epe->forget) text-cyan @endif  @if($epe->forced_mark) text-orange @endif" @if($epe->forced_mark) title="Cette note est obligatoire elle sera prise en compte dans le calcule de moyenne qu'elle soit meilleure note ou non" @endif @if($epe->forget) title="Cette note ne sera pas prise en compte pour le calcule de moyenne qu'elle soit meilleure note ou non" @endif > {{ $epe->value >= 10 ? $epe->value : '0'.$epe->value}} </span>
                                        </td>
                                    @endforeach
                                    @if ($marks[$p->id]['epe'] && count($marks[$p->id]['epe']) < $epeMaxLenght)
                                        @for ($e = (count($marks[$p->id]['epe']) + 1); $e <= $epeMaxLenght; $e++)
                                            <td wire:click="insertMarks({{$p->id}})" class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif
                                @else
                                    @for ($epev=1; $epev <= $epeMaxLenght; $epev++)
                                        <td wire:click="insertMarks({{$p->id}})" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                @endif

                                {{-- LES PARTICIPATIONS --}}
                                @if($marks[$p->id]['participation'])
                                    @foreach ($marks[$p->id]['participation'] as $l => $part)
                                        <td wire:click="setTargetedMark({{$part->id}})" class="text-center cursor-pointer @if($part->value == 0) text-warning @endif ">
                                            <span class="w-100 cursor-pointer @if($part->forget) text-black-50  @endif  @if($part->forced_mark) text-white-50 @endif" @if($part->forced_mark) title="Cette note est obligatoire elle sera prise en compte dans le calcule de moyenne qu'elle soit meilleure note ou non" @endif @if($part->forget) title="Cette note ne sera pas prise en compte pour le calcule de moyenne qu'elle soit meilleure note ou non" @endif > {{ $part->value >= 10 ? $part->value : '0'.$part->value}} </span>
                                        </td>
                                    @endforeach
                                    @if ($marks[$p->id]['participation'] && count($marks[$p->id]['participation']) < $participMaxLenght)
                                        @for ($part=(count($marks[$p->id]['participation']) + 1); $part <= $participMaxLenght; $part++)
                                            <td wire:click="insertMarks({{$p->id}}, 'participation')" class="text-center cursor-pointer">
                                                <span class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif
                                @else
                                    @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                        <td wire:click="insertMarks({{$p->id}}, 'participation')" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                @endif

                                {{-- LES DEVOIRS --}}
                                @if ($marks[$p->id]['dev'])
                                    @foreach ($marks[$p->id]['dev'] as $q => $dev)
                                        <td title="Devoir No {{$dev->mark_index}}" wire:click="setTargetedMark({{$dev->id}}, 'devoir')" class="text-center cursor-pointer">
                                            <span class="w-100 cursor-pointer @if($dev && $dev->value && $dev->value < 10) text-danger  @else text-primary @endif "> {{ $dev->value >= 10 ? $dev->value : '0'.$dev->value }} </span>
                                        </td>
                                    @endforeach
                                    @if ($marks[$p->id]['dev'] && count($marks[$p->id]['dev']) < $devMaxLenght)
                                        @for ($d=(count($marks[$p->id]['dev']) + 1); $d <= $devMaxLenght; $d++)
                                            <td wire:click="insertMarks({{$p->id}}, 'devoir')" class="text-center cursor-pointer">
                                                <span  class="w-100 cursor-pointer"> - </span>
                                            </td>
                                        @endfor
                                    @endif

                                @else
                                    @for ($dvv=1; $dvv <= $devMaxLenght; $dvv++)
                                        <td  wire:click="insertMarks({{$p->id}}, 'devoir')" class="text-center cursor-pointer">
                                            <span  class="w-100 cursor-pointer"> - </span>
                                        </td>
                                    @endfor
                                @endif
                                <td class=" text-center moy-epe-note {{$averageEPETab[$p->id] !== null ? ($averageEPETab[$p->id] >= 10 ? 'text-success' : 'text-danger') : 'text-warning' }} "> 
                                    @if($averageEPETab)
                                        {{ $averageEPETab[$p->id] !== null ? ($averageEPETab[$p->id] >= 10 ? $averageEPETab[$p->id] : '0'.$averageEPETab[$p->id]) : ' - ' }} 
                                    @else
                                        {{ ' - ' }}
                                    @endif
                                </td>
                                <td class=" text-center moy-note text-white-50 z-hover {{ $averageTab[$p->id] !== null ? ($averageTab[$p->id] >= 10 ? 'bg-success' : 'bg-danger') : 'bg-secondary' }} "> 
                                    @if($averageTab)
                                        {{ $averageTab[$p->id] !== null ? ($averageTab[$p->id] >= 10 ? $averageTab[$p->id] : '0'.$averageTab[$p->id]) : ' - ' }} 
                                    @else
                                       {{ ' - ' }}
                                    @endif
                                </td>
                                <td class=" text-center moy-coef-note">
                                    @if($averageTab) 
                                        {{ $averageTab[$p->id] !== null ? ($averageTab[$p->id] * $classe_subject_coef >= 10 ? ($averageTab[$p->id] * $classe_subject_coef) : '0'.($averageTab[$p->id] * $classe_subject_coef)) : ' - ' }}
                                    @else
                                        {{ ' - ' }}
                                    @endif
                                </td>
                                <td class=" text-center rank-note text-warning"> 
                                    @if($ranksTab)
                                        @isset($ranksTab[$p->id])
                                            <span>{{ $ranksTab[$p->id]['rank']}}</span><sup>{{ $ranksTab[$p->id]['exp']}}</sup>
                                            <small> {{ $ranksTab[$p->id]['base'] }} </small>
                                        @else
                                            {{ ' - ' }}
                                        @endif
                                    @else
                                        {{ ' - ' }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span wire:click="insertRelatedMark({{$p->id}})" title="Ajouter une note relative : Sanction ou Bonus" class="cursor-pointer">
                                        <strong class="bi-plus text-success"></strong>/
                                        <strong class="text-danger"> - </strong>
                                    </span>
                                </td>
                            @else
                                @for ($ev=1; $ev <= $epeMaxLenght; $ev++)
                                    <td class="text-center cursor-pointer">
                                        <span  class="w-100 cursor-pointer"> - </span>
                                    </td>
                                @endfor
                                @for ($part_v=1; $part_v <= $participMaxLenght; $part_v++)
                                    <td class="text-center cursor-pointer">
                                        <span class="w-100 cursor-pointer"> - </span>
                                    </td>
                                @endfor
                                @for ($dv=1; $dv <= $devMaxLenght; $dv++)
                                    <td class="text-center cursor-pointer">
                                        <span class="w-100 cursor-pointer"> - </span>
                                    </td>
                                @endfor
                                <td class=" text-center moy-epe-note"> </td>
                                <td class=" text-center moy-note"> - </td>
                                <td class=" text-center moy-coef-note"> - </td>
                                <td class=" text-center rank-note"> - </td>
                                <td class="text-center"> - </td>
                            @endif
                        </tr>
                    @endforeach
                </table> 
                <div class="d-flex justify-content-end w-100 my-1 p-2">
                    <span wire:click="printerToPDF" class="btn btn-success p-2 px-4 z-scale" title="Imprimer les notes de classe de la classe {{$classe->name}}">
                        <span class=" fa fa-print"></span>
                        <span>Print</span>
                    </span>
                </div>                                                    
            </div>
        @else
            <div>
                <div class="d-flex justify-content-center mx-auto mt-4  w-100">
                    <span class="fa fa-trash text-muted fa-8x"></span>
                </div>
                <div class="d-flex justify-content-center mx-auto mt-3 w-100">
                    <h4 class="letter-spacing-12 font-italic text-orange">OUUUPPPS, aucune note n'a été trouvé!!!</h4>
                </div>
                <blockquote class="text-warning">
                    <span class="float-right border-top border-white w-100 d-inline-block text-right">
                        <i class="text-warning small">OUPPPS pas de notes!!!!!</i>
                    </span>
                </blockquote>
            </div>
        @endif
        @if($noMarks || !$pupils)
        <div class="my-2 p-2 text-center border rounded text-white-50">
            <h6 class="mx-auto p-3 text-white-50">
                <h1 class="m-0 p-0">
                    <span class="bi-exclamation-triangle text-warning text-center p-2"></span>
                </h1>
                Il parait qu'aucune donnée n'est disponible pour cette classe de 
                <span class="text-warning">{{ $classe ? $classe->name : 'inconnue' }}</span> 
                pour le compte de l'année scolaire <span class="text-orange">{{ session('school_year_selected') }}</span> 
                pour le <span class="text-warning">{{ $semestre_type . ' ' . session('semestre_selected')}}</span>

                <blockquote class="text-info">
                    Veuillez sectionner un autre {{ $semestre_type }} ou une autre année scolaire
                </blockquote>
            </h6>
        </div>
        @endif
    </div>
    @endif

</div>
