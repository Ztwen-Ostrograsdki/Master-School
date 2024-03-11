<x-z-modal-generator :hasHeader="true" :modalHeaderTitle="$title" :width="6" :icon="'fa bi-person-check'" :modalName="'manageClasseReferees'" :modalBodyTitle="$title">
    <form autocomplete="off" class="form-group pb-3 px-2 bg-transparent" wire:submit.prevent="submit">
        <div class="row justify-between">
            <div class="mt-0 mb-2 col-12 mx-auto">

                @if($classe && $target)
                <div class="col-12 p-2 m-2 mx-auto">

                    @if($target == 'pp')

                        @php
                            $pp = $classe->currentPrincipal();
                        @endphp

                        @if($pp)
                            <h6 class="text-center text-white-50 p-1 border rounded w-100"> 
                                <span>Le prof principal actuel de cette classe est : </span>
                                <span class="text-warning">{{ $pp->getName() }}</span>
                            </h6>
                        @else
                            <h6 class="text-center text-warning p-1 border border-warning rounded"> 
                                <span>Cette classe n'a pas encore de prof principal</span>
                            </h6>
                        @endif

                    @elseif($target == 'r1')

                        @php
                            $rp1 = $classe->pupil_respo1();
                        @endphp

                        @if($rp1)
                            <h6 class="text-center text-white-50 p-1 border rounded w-100"> 
                                <span>Le premier responsable actuel de cette classe est : </span>
                                <span class="text-warning">{{ $rp1->getName() }}</span>
                            </h6>
                        @else
                            <h6 class="text-center text-warning p-1 border border-warning rounded"> 
                                <span>Cette classe n'a pas encore de premier responsable</span>
                            </h6>
                        @endif

                    @elseif($target == 'r2')
                        @php
                            $rp2 = $classe->pupil_respo2();
                        @endphp

                        @if($rp2)
                            <h6 class="text-center text-white-50 p-1 border rounded w-100"> 
                                <span>Le second responsable actuel de cette classe est : </span>
                                <span class="text-warning">{{ $rp2->getName() }}</span>
                            </h6>
                        @else
                            <h6 class="text-center text-warning p-1 border border-warning rounded"> 
                                <span>Cette classe n'a pas encore de second responsable</span>
                            </h6>
                        @endif
                    @endif

                </div>
                @endif


                <div class="d-flex row justify-content-between m-0 p-0">

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

                    @if($target == 'pp')
                    <div class="col-8">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le PP </label>
                        <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('teacher_id') text-danger border border-danger @enderror" wire:model="teacher_id" name="teacher_id">
                            <option class="" value="{{null}}">Choisissez le PP</option>
                            <option title="La classe n'aura plus de PP" value="{{'remove'}}">Supprimer</option>
                            @foreach ($teachers as $teacher)
                                <option  value="{{$teacher->id}}">{{$teacher->getFormatedName()}}</option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    @endif

                    @if($target == 'r1')
                    <div class="col-8">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le premier respo</label>
                        <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('respo1_id') text-danger border border-danger @enderror" wire:model="respo1_id" name="respo1_id">
                            <option class="" value="{{null}}">Choisissez le premier respo</option>
                            <option title="La classe n'aura plus de premier responsable" value="{{'remove'}}">Supprimer</option>
                            @foreach ($pupils as $p1)
                                <option @if($classe && $classe->pupil_respo2() && $classe->pupil_respo2()->id == $p1->id) disabled  title="Cet {{$p1->sexe == 'female' ? 'te' : ''}} apprenant {{$p1->sexe == 'female' ? 'e' : ''}} est {{$p1->sexe == 'female' ? 'la' : 'le'}} second {{$p1->sexe == 'female' ? 'e' : ''}} responsable: {{$p1->sexe == 'female' ? 'elle' : 'il'}} ne peut être choisir! " @endif  value="{{$p1->id}}">{{$p1->getName()}}</option>
                            @endforeach
                        </select>
                        @error('respo1_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    @endif

                    @if($target == 'r2')
                    <div class="col-8">
                        <label class="z-text-cyan m-0 p-0 w-100 cursor-pointer" for="">Le 2nd respo</label>
                        <select class="px-2 form-select text-white custom-select z-bg-secondary w-100 @error('respo2_id') text-danger border border-danger @enderror" wire:model="respo2_id" name="respo2_id">
                            <option class="" value="{{null}}">Choisissez le 2nd respo</option>
                            <option title="la classe n'aura plus de second responsable" value="{{'remove'}}">Supprimer</option>
                            @foreach ($pupils as $p2)
                                <option @if($classe && $classe->pupil_respo2() && $classe->pupil_respo2()->id == $p2->id) disabled  title="Cet {{$p2->sexe == 'female' ? 'te' : ''}} apprenant {{$p2->sexe == 'female' ? 'e' : ''}} est {{$p2->sexe == 'female' ? 'la' : 'le'}} second {{$p2->sexe == 'female' ? 'e' : ''}} responsable: {{$p2->sexe == 'female' ? 'elle' : 'il'}} ne peut être choisir! " @endif  value="{{$p2->id}}">{{$p2->getName()}}</option>
                            @endforeach
                        </select>
                        @error('respo2_id')
                            <small class="py-1 z-text-orange">{{$message}}</small>
                        @enderror
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="p-0 m-0 mx-auto d-flex justify-content-center pb-1 pt-1">
            <x-z-button :bg="'btn-primary'" class="text-dark">Mettre à jour</x-z-button>
        </div>
    </form>
</x-z-modal-generator>
    