<div class="m-0 p-0 w-100">
    <div class="z-justify-relative-top-80 w-100" style="width: 90%;" >
        <div class="w-100 border m-0 p-0">
            <div class="m-0 p-0 w-100"> 
                <div class="row w-100 m-0 d-fex justify-content-between">
                   <div class="col-12 m-0 p-0 p-1 pt-0 float-right">
                        <div class="z-bg-secondary border m-0 p-0">
                            <div class="mx-auto w-100 px-2">
                                <h5 class="text-white-50 pt-2 pb-1 d-flex justify-content-between">
                                    <span class="text-left h4 text-cursive text-orange text-uppercase">
                                        <span class="fa bi-tools"></span>
                                        Profil de gestion de classe pour enseignant
                                    </span>
                                    <span class="text-center fa-2x">
                                        @if($classe)
                                            @php
                                                $cl = $classe->getNumericName();
                                            @endphp
                                            <span class="mx-2"></span>
                                            <span class="">
                                                {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                            </span>
                                        @else
                                            <span>Classe inconnue</span>
                                        @endif
                                    </span>
                                </h5>
                                <h6 class="text-white"> 
                                    <span class="info-box-text">Effectif 
                                        (<b class="text-warning">
                                            {{ $classe ? count($classe->getPupils(session('school_year_selected'))) : 'vide'}}
                                        </b>)
                                    </span>
                                    <span class="info-box-number">
                                        {{ $classe ? 
                                            'G: ' . count($classe->getClassePupilsOnGender('male', session('school_year_selected'))) . ' 
                                            - F: '. count($classe->getClassePupilsOnGender('female', session('school_year_selected'))) : ' vide'
                                        }}
                                    </span>
                                    <span class="float-right">
                                        @if($classe && $classe->classe_group)
                                            <span class="text-success mx-1">
                                                Promotion {{ $classe->classe_group->name }}
                                            </span>
                                            <span class="ml-3">Coef:  {{ $classe_subject_coef }}</span>
                                        @endif
                                    </span>
                                </h6>
                                <hr class="m-0 p-0 text-white w-100">
                                <div class="m-0 p-0 d-flex justify-content-between w-100">
                                    <div class="m-0 p-0 w-50 d-flex">
                                        <div class="col-1 p-0 m-0 ">
                                            <div class="m-0" id="OpenEditPhotoProfilModal" title="Doucle cliquer pour changer la photo de profil">
                                               <div class="d-flex w-100 justify-content-between cursor-pointer m-0 p-0 border">
                                                    @if($user)
                                                        <img src="{{$user->__profil('250')}}" alt="mon profil" class="w-100">
                                                    @endif
                                               </div>
                                           </div>
                                        </div>
                                        <div class="d-flex flex-column col-5">
                                            <h6 class="text-white-50 pt-2 pb-1">
                                                <span class="bi-person-badge"></span>
                                                Compte: 
                                                <a class="text-white-50" href="{{route('user_profil', $user->id)}}">
                                                    {{  $user->pseudo . '  (' .  $user->email . ')' }}
                                                </a>
                                                <span class="fa fa-circle text-success"></span>
                                            </h6>
                                            <h6 class="text-white-50 pt-2 pb-1">
                                                <span class="bi-person"></span>
                                                Prof: {{  $user->teacher->name . ' ' .  $user->teacher->surname }}
                                            </h6>
                                            <h6 class="text-white-50 pb-1">
                                                <span class="bi-shield-check"></span>
                                                Matière: {{  $user->teacher->speciality()->name }}
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end float-right">
                                        <h6 class="mt-2">
                                            <span class="text-warning">Mes classes:</span>
                                            <span>
                                                @if($user->teacher->hasClasses())
                                                    @foreach($user->teacher->getTeachersCurrentClasses() as $c)
                                                        @php
                                                            $cl = $c->getNumericName();
                                                        @endphp
                                                        <a class="text-white border rounded border-white btn-secondary py-1 px-2 mr-1 my-1" href="{{route('teacher_profil_as_user', ['id' => auth()->user()->id, 'slug' => $c->slug])}}">
                                                            {{ $cl['root'] }}<sup>{{ $cl['sup'] }} </sup> {{ $cl['idc'] }}
                                                        </a>
                                                    @endforeach
                                                @else
                                                    Aucune classe assignée!
                                                @endif
                                            </span>
                                        </h6>
                                    </div>
                                </div>
                                <hr class="m-0 p-0 bg-white text-white">
                            </div>
                            
                            <div class="px-2" style="min-height: 700px; overflow: auto">
                                <div class="d-flex w-100 justify-content-between">
                                    <form class=" bg-dark m-2 w-25 rounded" action="">
                                        @csrf()
                                        <select wire:model="semestre_selected" wire:change="changeSemestre" class="form-select bg-secondary-dark custom-select z-bg-  m-2 zw-95 text-white">
                                          <option value="{{null}}">Veuillez sélectionner le {{$semestre_type}}</option>
                                          @foreach ($semestres as $semestre)
                                              <option value="{{$semestre}}">{{$semestre_type . ' ' . $semestre}}</option>
                                          @endforeach
                                        </select>
                                    </form>
                                    <div>
                                        <h4 class=" text-warning mt-2 text-uppercase text-center"> {{ $section_to_display ? $titles[$section_to_display] : '' }} </h4>
                                    </div>
                                    <form class=" bg-dark m-2 w-25 rounded" action="">
                                        @csrf()
                                        <select wire:model="section_to_display" wire:change="changeSection" class="form-select text-uppercase bg-secondary-dark custom-select z-bg-  m-2 zw-95 text-white">
                                          <option value="{{null}}">Veuillez sélectionner la section à afficher</option>
                                          <option value="liste">La liste</option>
                                          <option value="marks">Les notes</option>
                                          <option value="related_marks">Les bonus - sanctions</option>
                                          <option value="absences">Les Absences</option>
                                          <option value="classe_general_stats">Stats Générales</option>
                                        </select>
                                    </form>
                                </div>
                                <hr class="m-0 p-0 bg-white text-white">
                                <div>
                                    @if($section_to_display == 'marks')
                                        @livewire('classe-marks-lister', ['classe_id' => $classe->id, 'subject_selected' => $user->teacher->speciality(), 'classe_subject_selected' => $user->teacher->speciality()->id, 'semestre_selected' => $semestre_selected])
                                    @elseif($section_to_display == 'liste')
                                        @livewire('classe-pupils-lister', ['classe_id' => $classe->id])

                                    @elseif($section_to_display == 'related_marks')
                                        @livewire('classe-pupil-related-mark', ['classe_id' => $classe->id])
                                    @elseif($section_to_display == 'classe_general_stats')
                                        @livewire('classe-generals-stats', ['classe_id' => $classe->id, 'semestre_selected' => $semestre_selected, 'subject_selected' => $user->teacher->speciality()->id, 'teacher_id' => $user->teacher->id])
                                    @elseif($section_to_display == 'absences')
                                        @livewire('classe-presence-absence', ['classe_id' => $classe->id, 'semestre_selected' => $semestre_selected, 'classe_subject_selected' => $user->teacher->speciality()->id])
                                    @else
                                        <blockquote class="">
                                            <h6 class="h6 text-white-50">
                                                Veuillez sélectionner une section valide!
                                            </h6>
                                        </blockquote>
                                    @endif
                                </div>

                            </div>

                        </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
</div>