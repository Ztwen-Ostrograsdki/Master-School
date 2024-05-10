<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Mark;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobUpdateClasseParticipationMarks implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $classe;

    public $school_year_model;

    public $subject;

    public $semestre = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, SchoolYear $school_year_model, $semestre, Subject $subject, User $user)
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->subject = $subject;

        $this->semestre = $semestre;


    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function($e){

            $school_year_model = $this->school_year_model;

            $classe = $this->classe;

            $semestre = $this->semestre;

            $subject = $this->subject;

            $user = $this->user;


            $pupils = $classe->getNotAbandonnedPupils();


            if(count($pupils) > 0){

                foreach($pupils as $pupil){

                    $participation = $pupil->definedParticipationMark($semestre, $subject->id);


                    $old = $pupil->marks()->where('marks.school_year_id', $school_year_model->id)
                                   ->where('marks.subject_id', $subject->id)
                                   ->where('marks.classe_id', $classe->id)
                                   ->where('marks.semestre', $semestre)
                                   ->where('marks.type', 'participation')
                                   ->first();

                    if($old && $old->value !== $participation){

                        $old->update(['value' => $participation]);

                    }
                    else{

                        $part_mark = Mark::create([
                                'value' => $participation, 
                                'pupil_id' => $pupil->id, 
                                'user_id' => $user->id, 
                                'creator' => $user->id, 
                                'subject_id' => $subject->id, 
                                'school_year_id' => $school_year_model->id, 
                                'classe_id' => $classe->id, 
                                'semestre' => $semestre, 
                                'type' => 'participation', 
                                'mark_index' => 1, 
                                'level_id' => $pupil->level_id, 
                            ]);

                            if ($part_mark) {
                                
                                $school_year_model->marks()->attach($part_mark->id);
                            }

                    }


                }


            }


        });
    }
}
