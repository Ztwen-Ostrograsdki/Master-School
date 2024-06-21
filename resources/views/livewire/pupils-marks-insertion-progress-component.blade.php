<div>
    <div class=" mx-auto" style="position: relative;">
        <div class="mx-auto bg-transparent-opac">
            <div class="m-0 p-0 w-100">
                <blockquote class="text-warning p-0">
                    <hr class=" w-100 m-0 p-0 bg-orange">
                    <h6 style="letter-spacing: 1.2px" class="w-100 py-2 m-0 fx-17 text-orange px-2 mr-2 font-italic">
                        <span>EXECUTION DES TACHES PLANNIFIEES</span>
                        @if($failed_jobs)
                            <span class="text-right float-right text-warning"> 
                                {{ $failed_jobs }} tâches ont échouées! 
                            </span>
                        @endif
                    </h6>
                    <hr class=" w-100 m-0 p-0 bg-primary">
                </blockquote>

                <div class="mx-auto px-3">
                    @if($failed_jobs)
                        <div>
                            <span title="Supprimer les tâches échouées" wire:click="deleteFailsJobs" class="btn px-3 btn-warning">
                                <span>Supprimer ces tâches</span>
                            </span>

                            <span title="Relancer les tâches échouées" wire:click="retryFailsJobs" class="btn px-3 btn-primary">
                                <span>Relancer ces tâches</span>
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if($classe)
        <div wire:poll.visible.1s class="col-12 mx-auto bg-transparent-opac" style="opacity: 0.8;">
            <div class="w-100 mx-auto p-1">
                <div class="w-100 z-color-orange">
                    <h5 class="text-center w-100">
                        
                    </h5>
                    <hr class="w-100 z-border-orange mx-auto my-2">
                </div>
                <div class="w-100 bg-secondary-light-3 mb-2 border rounded p-2">
                    <h6>TACHES D'INSERTION DE NOTES DANS LA BASE DE DONNEES</h6>
                    @if(count($marks_insertion_batches))
                        @foreach($marks_insertion_batches as $batch)
                            <div class="my-2 border rounded @if($batch->hasFailures()) bg-danger @endif">
                                @php
                                    $subject = null;

                                    $details = $classe->marks_batches()->where('batch_id', $batch->id)->where('user_id', auth()->user()->id)->first();

                                    if($details){

                                        if($details->subject_id){

                                            $subject = $details->subject;

                                        }
                                    }
                                @endphp

                                <h6 class="text-warning text-right m-0 p-0">
                                    @if($details)
                                        <p class="px-1 pr-2">
                                            Details: @if($details->method_type == 'insertion') Insertion @else Suppression @endif de {{ $details->total_marks }} notes @if($subject) de {{ $subject->name }} @else dans toutes les matières  @endif
                                        </p>

                                    @endif
                                </h6>

                                <div class="d-flex justify-between">
                                    <div class="my-2 col-8">
                                        @php
                                            $prog = $batch->progress();
                                        @endphp
                                        <div class="progress" style="height: 12px;">

                                            <div class="progress-bar progress-bar-striped border @if(!$batch->finished() ) @endif rounded @if($batch->hasFailures()) bg-danger @endif @if($batch->finished()) bg-success @endif @if(!$batch->finished() && !$batch->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog}}%;" aria-valuenow="$prog" aria-valuemin="0" aria-valuemax="100">
                                                {{$prog}} %
                                            </div>
                                        </div>
                                        <span> {{ $prog }} % </span>
                                        @if($batch->hasFailures())
                                            <span class=" ml-2 letter-spacing-12">Opération échouée</span>
                                        @else
                                            <small class="text-white letter-spacing-12 float-right">
                                                Terminée {{ Illuminate\Support\Carbon::parse($batch->finishedAt)->diffForHumans() }}
                                            </small>
                                        @endif
                                        
                                        <small class="text-orange letter-spacing-12 float-right mx-2"> {{ $batch->id }} </small>

                                    </div>
                                    <div class="d-flex justify-between  p-1">
                                        @if($batch->hasFailures())
                                            <span wire:click="retryXBatch('{{$batch->id}}')" class="btn btn-primary p-2 mr-2">
                                                <span class="fa fa-recycle"></span>
                                                <span>Relancer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished() && !$batch->hasFailures())
                                            <span wire:click="cancelXBatch('{{$batch->id}}')" class="btn btn-info p-2 mr-2">
                                                <span class="fa fa-square text-danger"></span>
                                                <span>Annuler</span>
                                            </span>
                                        @endif

                                        @if($batch->finished() || $batch->hasFailures())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Supprimer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Annuler et Suppr</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <h6 class="text-warning letter-spacing-12">Aucune tâche en cours</h6>
                    @endif
                </div>

                <div class="w-100 bg-secondary-light-1 mb-2 border rounded p-2">
                    <h6>TACHES DE SUPPRESSION DE NOTES DANS LA BASE DE DONNEES</h6>
                    @if(count($marks_deletion_batches))
                        @foreach($marks_deletion_batches as $batch)
                            <div class="my-2 border rounded @if($batch->hasFailures()) bg-danger @endif">
                                @php
                                    $subject = null;

                                    $details = $classe->marks_batches()->where('batch_id', $batch->id)->where('user_id', auth()->user()->id)->first();

                                    if($details){

                                        if($details->subject_id){

                                            $subject = $details->subject;

                                        }
                                    }
                                @endphp

                                <h6 class="text-warning text-right m-0 p-0">
                                    @if($details)
                                        <p class="px-1 pr-2">
                                            Details: @if($details->method_type == 'insertion') Insertion @else Suppression @endif de {{ $details->total_marks }} notes @if($subject) de {{ $subject->name }} @else dans toutes les matières  @endif
                                        </p>

                                    @endif
                                </h6>

                                <div class="d-flex justify-between">
                                    <div class="my-2 col-8">
                                        @php
                                            $prog = $batch->progress();
                                        @endphp
                                        <div class="progress" style="height: 12px;">

                                            <div class="progress-bar progress-bar-striped border @if(!$batch->finished() ) @endif rounded @if($batch->hasFailures()) bg-danger @endif @if($batch->finished()) bg-success @endif @if(!$batch->finished() && !$batch->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog}}%;" aria-valuenow="$prog" aria-valuemin="0" aria-valuemax="100">
                                                {{$prog}}%

                                            </div>
                                        </div>
                                        <span> {{ $prog }} % </span>
                                        @if($batch->hasFailures())
                                            <span class=" ml-2 letter-spacing-12">Opération échouée</span>
                                        @else
                                            <small class="text-white letter-spacing-12 float-right">
                                                Terminée {{ Illuminate\Support\Carbon::parse($batch->finishedAt)->diffForHumans() }}
                                            </small>
                                        @endif
                                        
                                        <small class="text-orange letter-spacing-12 float-right mx-2"> {{ $batch->id }} </small>

                                    </div>
                                    <div class="d-flex justify-between  p-1">
                                        @if($batch->hasFailures())
                                            <span wire:click="retryXBatch('{{$batch->id}}')" class="btn btn-primary p-2 mr-2">
                                                <span class="fa fa-recycle"></span>
                                                <span>Relancer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished() && !$batch->hasFailures())
                                            <span wire:click="cancelXBatch('{{$batch->id}}')" class="btn btn-info p-2 mr-2">
                                                <span class="fa fa-square text-danger"></span>
                                                <span>Annuler</span>
                                            </span>
                                        @endif

                                        @if($batch->finished() || $batch->hasFailures())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Supprimer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Annuler et Suppr</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <h6 class="text-warning letter-spacing-12">Aucune tâche en cours</h6>
                    @endif
                </div>


                <div class="w-100 mb-2 bg-secondary-light-0 border rounded p-2">
                    <h6>TACHES DE MISE A JOUR DE NOTES DANS LA BASE DE DONNEES</h6>
                    @if(count($db_updating_marks_batches))
                        @foreach($db_updating_marks_batches as $batch)
                            <div class="my-2 border rounded @if($batch->hasFailures()) bg-danger @endif">

                                @php
                                    $subject = null;

                                    $details = $classe->marks_batches()->where('batch_id', $batch->id)->where('user_id', auth()->user()->id)->first();

                                    if($details){

                                        if($details->subject_id){

                                            $subject = $details->subject;

                                        }
                                    }
                                @endphp

                                <h6 class="text-warning text-right m-0 p-0">
                                    @if($details)
                                        <p class="px-1 pr-2">
                                            Details: @if($details->method_type == 'insertion') Insertion @else Suppression @endif de {{ $details->total_marks }} notes @if($subject) de {{ $subject->name }} @else dans toutes les matières  @endif
                                        </p>

                                    @endif
                                </h6>

                                <div class="d-flex justify-between">
                                    <div class="my-2 col-8">
                                        @php
                                            $prog = $batch->progress();
                                        @endphp
                                        <div class="progress" style="height: 12px;">

                                            <div class="progress-bar progress-bar-striped border @if(!$batch->finished() ) progress-bar-animated @endif rounded @if($batch->hasFailures()) bg-danger @endif @if($batch->finished()) bg-success @endif @if(!$batch->finished() && !$batch->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog}}%;" aria-valuenow="$prog" aria-valuemin="0" aria-valuemax="100">
                                                {{$prog}}%

                                            </div>
                                        </div>
                                        <span> {{ $prog }} % </span>
                                        @if($batch->hasFailures())
                                            <span class=" ml-2 letter-spacing-12">Opération échouée</span>
                                        @else
                                            <small class="text-white letter-spacing-12 float-right">
                                                Terminée {{ Illuminate\Support\Carbon::parse($batch->finishedAt)->diffForHumans() }}
                                            </small>
                                        @endif
                                        
                                        <small class="text-orange letter-spacing-12 float-right mx-2"> {{ $batch->id }} </small>

                                    </div>
                                    <div class="d-flex justify-between  p-1">
                                        @if($batch->hasFailures())
                                            <span wire:click="retryXBatch('{{$batch->id}}')" class="btn btn-primary p-2 mr-2">
                                                <span class="fa fa-recycle"></span>
                                                <span>Relancer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished() && !$batch->hasFailures())
                                            <span wire:click="cancelXBatch('{{$batch->id}}')" class="btn btn-info p-2 mr-2">
                                                <span class="fa fa-square text-danger"></span>
                                                <span>Annuler</span>
                                            </span>
                                        @endif

                                        @if($batch->finished() || $batch->hasFailures())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Supprimer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Annuler et Suppr</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <h6 class="text-warning letter-spacing-12">Aucune tâche en cours</h6>
                    @endif
                </div>

                <div class="w-100 mb-2 bg-secondary-light-3 border rounded p-2">
                    <h6>TACHES D'EDITION DE NOTES</h6>
                    @if(count($trying_to_update_pupil_mark_batches))
                        @foreach($trying_to_update_pupil_mark_batches as $batch)
                            <div class="my-2 border rounded @if($batch->hasFailures()) bg-danger @endif">

                                @php
                                    $subject = null;

                                    $details = $classe->marks_batches()->where('batch_id', $batch->id)->where('user_id', auth()->user()->id)->first();

                                    if($details){

                                        if($details->subject_id){

                                            $subject = $details->subject;

                                        }
                                    }
                                @endphp

                                <h6 class="text-warning text-right m-0 p-0">
                                    @if($details)
                                        <p class="px-1 pr-2">
                                            Details: @if($details->method_type == 'updating') Mise à jour @endif de {{ $details->total_marks }} notes @if($subject) de {{ $subject->name }} @else dans toutes les matières  @endif
                                        </p>

                                    @endif
                                </h6>

                                <div class="d-flex justify-between">
                                    <div class="my-2 col-8">
                                        @php
                                            $prog = $batch->progress();
                                        @endphp
                                        <div class="progress" style="height: 12px;">

                                            <div class="progress-bar progress-bar-striped border @if(!$batch->finished() ) progress-bar-animated @endif rounded @if($batch->hasFailures()) bg-danger @endif @if($batch->finished()) bg-success @endif @if(!$batch->finished() && !$batch->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog}}%;" aria-valuenow="$prog" aria-valuemin="0" aria-valuemax="100">
                                                {{$prog}}%

                                            </div>
                                        </div>
                                        <span> {{ $prog }} % </span>
                                        @if($batch->hasFailures())
                                            <span class=" ml-2 letter-spacing-12">Opération échouée</span>
                                        @else
                                            <small class="text-white letter-spacing-12 float-right">
                                                Terminée {{ Illuminate\Support\Carbon::parse($batch->finishedAt)->diffForHumans() }}
                                            </small>
                                        @endif
                                        
                                        <small class="text-orange letter-spacing-12 float-right mx-2"> {{ $batch->id }} </small>

                                    </div>
                                    <div class="d-flex justify-between  p-1">
                                        @if($batch->hasFailures())
                                            <span wire:click="retryXBatch('{{$batch->id}}')" class="btn btn-primary p-2 mr-2">
                                                <span class="fa fa-recycle"></span>
                                                <span>Relancer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished() && !$batch->hasFailures())
                                            <span wire:click="cancelXBatch('{{$batch->id}}')" class="btn btn-info p-2 mr-2">
                                                <span class="fa fa-square text-danger"></span>
                                                <span>Annuler</span>
                                            </span>
                                        @endif

                                        @if($batch->finished() || $batch->hasFailures())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Supprimer</span>
                                            </span>
                                        @endif

                                        @if(!$batch->finished())
                                            <span wire:click="deleteXBatch('{{$batch->id}}')" class="btn btn-warning p-2">
                                                <span class="fa fa-trash"></span>
                                                <span>Annuler et Suppr</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        <h6 class="text-warning letter-spacing-12">Aucune tâche en cours</h6>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
    
    
</div>