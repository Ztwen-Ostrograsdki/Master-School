<div class=" mx-auto">
    @if($classe && !$no_batching)
    <div wire:poll.visible.100ms class="col-12 mx-auto bg-transparent-opac" style="opacity: 0.8;">
        <div class="w-100 mx-auto rounded mb-2 border-bottom border-orange bg-secondary-light-3 p-1">
            <div class="w-100 bg-transparen p-1">
                @if(count($marks_insertion_batches))
                    @foreach($marks_insertion_batches as $batch_insert)
                        <div class="my-1 @if($batch_insert->finished()) d-none @endif @if($batch_insert->hasFailures()) bg-danger @endif">
                            @php
                                $subject_insert = null;

                                $details = $classe->marks_batches()->where('batch_id', $batch_insert->id)->where('user_id', auth()->user()->id)->first();

                                if($details){

                                    if($details->subject_id){

                                        $subject_insert = $details->subject;

                                    }
                                }
                            @endphp

                            <div class="d-flex justify-between">
                                <div class="my-1 col-12">
                                    @php
                                        $prog = $batch_insert->progress();
                                    @endphp
                                    <div class="progress" style="height: 12px;">

                                        <div class="progress-bar progress-bar-striped border @if(!$batch_insert->finished() ) @endif rounded @if($batch_insert->hasFailures()) bg-danger @endif @if($batch_insert->finished()) bg-success @endif @if(!$batch_insert->finished() && !$batch_insert->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog}}%;" aria-valuenow="$prog" aria-valuemin="0" aria-valuemax="100">
                                            {{$prog}} %
                                        </div>
                                    </div>
                                    <span> {{ $prog }} % </span>
                                    <small class="text-warning letter-spacing-12 float-right mx-2">
                                        @if($details->method_type == 'insertion') Insertion @else Suppression @endif de {{ $details->total_marks }} notes @if($subject_insert) de {{ $subject_insert->name }} @else dans toutes les matières  @endif en cours ...
                                    </small>

                                </div>
                            </div>
                        </div>
                    @endforeach

                @endif
            </div>

            <div class="w-100 bg-transparent p-1">
                @if(count($marks_deletion_batches))
                    @foreach($marks_deletion_batches as $batch_del)
                        <div class="my-2  @if($batch_del->finished()) d-none @endif @if($batch_del->hasFailures()) bg-danger @endif">
                            @php
                                $subject_del = null;

                                $details = $classe->marks_batches()->where('batch_id', $batch_del->id)->where('user_id', auth()->user()->id)->first();

                                if($details){

                                    if($details->subject_id){

                                        $subject_del = $details->subject;

                                    }
                                }
                            @endphp

                            <div class="d-flex justify-between">
                                <div class="my-2 col-12">
                                    @php
                                        $prog_del = $batch_del->progress();
                                    @endphp
                                    <div class="progress" style="height: 12px;">

                                        <div class="progress-bar progress-bar-striped border @if(!$batch_del->finished() ) @endif rounded @if($batch_del->hasFailures()) bg-danger @endif @if($batch_del->finished()) bg-success @endif @if(!$batch_del->finished() && !$batch_del->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog_del}}%;" aria-valuenow="$prog_del" aria-valuemin="0" aria-valuemax="100">
                                            {{$prog_del}}%

                                        </div>
                                    </div>
                                    <span> {{ $prog_del }} % </span>
                                    
                                    <small class="text-warning letter-spacing-12 float-right mx-2"> 
                                        @if($details->method_type == 'insertion') Insertion @else Suppression @endif de {{ $details->total_marks }} notes @if($subject_del) de {{ $subject_del->name }} @else dans toutes les matières  @endif en cours...
                                    </small>

                                </div>
                            </div>
                        </div>
                    @endforeach

                @endif
            </div>

            <div class="w-100 mb-2 bg-transparent p-1">
                @if(count($db_updating_marks_batches))
                    @foreach($db_updating_marks_batches as $batch_up_db)
                        <div class="my-1 @if($batch_up_db->finished()) d-none @endif  @if($batch_up_db->hasFailures()) bg-danger @endif">

                            @php
                                $subject_up_db = null;

                                $details = $classe->marks_batches()->where('batch_id', $batch_up_db->id)->where('user_id', auth()->user()->id)->first();

                                if($details){

                                    if($details->subject_id){

                                        $subject_up_db = $details->subject;

                                    }
                                }
                            @endphp

                            <div class="d-flex justify-between">
                                <div class="my-1 col-12">
                                    @php
                                        $prog_up_db = $batch_up_db->progress();
                                    @endphp
                                    <div class="progress" style="height: 12px;">

                                        <div class="progress-bar progress-bar-striped border @if(!$batch_up_db->finished() ) progress-bar-animated @endif rounded @if($batch_up_db->hasFailures()) bg-danger @endif @if($batch_up_db->finished()) bg-success @endif @if(!$batch_up_db->finished() && !$batch_up_db->hasFailures()) bg-primary @endif " role="progressbar" style="width: {{$prog_up_db}}%;" aria-valuenow="$prog_up_db" aria-valuemin="0" aria-valuemax="100">

                                        </div>
                                    </div>
                                    <span> {{ $prog_up_db }} % </span>
                                    <small class="text-warning letter-spacing-12 float-right mx-2">  Mise à jour de la base de données des moyennes en cours... </small>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
