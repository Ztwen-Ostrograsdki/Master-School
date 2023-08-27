<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Mark;
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

class JobInsertClassePupilMarksTogether implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $data = [];

    public $user;

    public $subject;

    public $marks = [];

    public $semestre = 1;

    public $school_year_model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;

        $this->classe = $data['classe'];

        $this->user = $data['user'];

        $this->subject = $data['subject'];

        $this->marks = $data['marks'];

        $this->semestre = $data['semestre'];

        $this->school_year_model = $data['school_year_model'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->doJob();
    }


    public function doJob()
    {

        DB::transaction(function($e){

            $classe = $this->classe;

            $marks = $this->marks;

            $semestre = $this->semestre;

            $subject = $this->subject;

            $school_year_model = $this->school_year_model;

            $classe_id = $classe->id;

            $subject_id = $subject->id;

            $user = $this->user;


            if($classe && $marks && $semestre && $school_year_model && $subject){

                if(true){

                    foreach($marks as $pupil_id => $data){

                        $pupil = $school_year_model->findPupil($pupil_id);

                        $pupil_id = $pupil_id;

                        $epe_marks = $data['epe'];

                        $dev_marks = $data['devoir'];

                        if($epe_marks || $dev_marks){

                            $epes = [];

                            $devs = [];

                            if($epe_marks){
                                $epes = explode('-', $epe_marks);
                            } 

                            if($dev_marks){
                                $devs = explode('-', $dev_marks);
                            }
                            
                            $tabs = [];

                            $epe_tabs = [];

                            $dev_tabs = [];

                            $epe_key_index = 1;

                            $dev_key_index = 1;

                            
                            if($epes !== []){
                                $has_epe_marks_index = $school_year_model->marks()
                                                                         ->where('pupil_id', $pupil->id)
                                                                         ->where('classe_id', $classe_id)
                                                                         ->where('subject_id', $subject_id)
                                                                         ->where('semestre', $semestre)
                                                                         ->where('type', 'epe')
                                                                         ->pluck('mark_index')
                                                                         ->toArray();

                                if(count($has_epe_marks_index) > 0){

                                    $epe_mark_index = max($has_epe_marks_index) + 1;
                                }
                                else{

                                   $epe_mark_index = 1;
                                }

                                $epe_key_index = $epe_mark_index;

                                foreach($epes as $epe){

                                    $mark_index_was_existed = $pupil->marks()
                                                                    ->where('classe_id', $classe_id)
                                                                    ->where('subject_id', $subject_id)
                                                                    ->where('marks.school_year_id', $school_year_model->id)
                                                                    ->where('semestre', $semestre)->where('type', "epe")
                                                                    ->where('mark_index', $epe_key_index)
                                                                    ->first();

                                    if(!$mark_index_was_existed){

                                        $epe_tabs[$epe_key_index] = floatval($epe);

                                        $epe_key_index++;

                                    }
                                }

                            }

                            if($devs !== []){
                                $has_dev_marks_index = $school_year_model->marks()
                                                                         ->where('pupil_id', $pupil->id)
                                                                         ->where('classe_id', $classe_id)
                                                                         ->where('subject_id', $subject_id)
                                                                         ->where('semestre', $semestre)
                                                                         ->where('type', 'devoir')
                                                                         ->pluck('mark_index')
                                                                         ->toArray();

                                if(count($has_dev_marks_index) > 0){

                                    $dev_mark_index = max($has_dev_marks_index) + 1;
                                }
                                else{
                                   $dev_mark_index = 1;
                                }

                                $dev_key_index = $dev_mark_index;

                                foreach($devs as $dev){

                                    $mark_index_was_existed = $pupil->marks()
                                                                    ->where('classe_id', $classe_id)->where('subject_id', $subject_id)
                                                                    ->where('semestre', $semestre)
                                                                    ->where('marks.school_year_id', $school_year_model->id)
                                                                    ->where('type', "devoir")
                                                                    ->where('mark_index', $dev_key_index)
                                                                    ->first();

                                    if(!$mark_index_was_existed){

                                        $dev_tabs[$dev_key_index] = floatval($dev);

                                        $dev_key_index++;

                                    }
                                    
                                }
                            }

                            if($epe_tabs !== []){
                                    
                                foreach($epe_tabs as $epe_k_index => $validEpe){

                                    $epe_mark = Mark::create([
                                        'value' => $validEpe, 
                                        'pupil_id' => $pupil->id, 
                                        'user_id' => $user->id, 
                                        'creator' => $user->id, 
                                        'subject_id' => $subject_id, 
                                        'school_year_id' => $school_year_model->id, 
                                        'classe_id' => $classe_id, 
                                        'semestre' => $semestre, 
                                        'type' => 'epe', 
                                        'mark_index' => $epe_k_index, 
                                        'level_id' => $pupil->level_id, 
                                    ]);

                                    if ($epe_mark) {
                                        
                                        $school_year_model->marks()->attach($epe_mark->id);
                                    }

                                }
                            }

                            if($dev_tabs !== []){
                                    
                                foreach($dev_tabs as $dev_k_index => $validDev){

                                    $dev_mark = Mark::create([
                                        'value' => $validDev, 
                                        'pupil_id' => $pupil->id, 
                                        'user_id' => $user->id, 
                                        'creator' => $user->id, 
                                        'school_year_id' => $school_year_model->id, 
                                        'subject_id' => $subject_id, 
                                        'classe_id' => $classe_id, 
                                        'semestre' => $semestre, 
                                        'type' => 'devoir', 
                                        'mark_index' => $dev_k_index, 
                                        'level_id' => $pupil->level_id, 
                                    ]);
                                    if ($dev_mark) {

                                        $school_year_model->marks()->attach($dev_mark->id);
                                    }

                                }
                            }

                        }

                    }

                }

            }

        });
        

    }
}
