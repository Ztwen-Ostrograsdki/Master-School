<?php

namespace App\Jobs;

use App\Events\TeacherCreatingOrUpdatingFailedEvent;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobCreateNewTeacher implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $name;

    public $surname;

    public $nationality;

    public $contacts;

    public $marital_status;

    public $level_id;

    public $user;

    public $school_year_model;

    public $subject;

    public $teacher;

    public $updating = false;

    public $edit_subject = false;

    public $updating_subject = false;

    public $old_subject;

    public $teacher;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contacts, $level_id, $name, $nationality, $marital_status, SchoolYear $school_year_model, Subject $subject, $surname, User $user, $updating, $updating_subject = false, $old_subject = null)
    {
        $this->name = $name;

        $this->surname = $surname;

        $this->nationality = $nationality;

        $this->contacts = $contacts;

        $this->marital_status = $marital_status;

        $this->level_id = $level_id;

        $this->user = $user;

        $this->school_year_model = $school_year_model;

        $this->subject = $subject;

        $this->updating = $updating;

        $this->updating_subject = $updating_subject;

        $this->edit_subject = $updating_subject;

        $this->old_subject = $old_subject;

        if($updating_subject || $updating){

            $teacher = $this->user->teacher;

            if($teacher){

                $this->teacher = $teacher;

            }

        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function($e){

            $subject = $this->subject;

            $school_year_model = $this->school_year_model;

            if($this->creating){
                try {
                    $teacher = Teacher::create([
                        'name' => strtoupper($this->name),
                        'surname' => ucwords($this->surname),
                        'nationality' => $this->nationality,
                        'contacts' => $this->contacts,
                        'marital_status' => $this->marital_status,
                        'level_id' => $this->level_id,
                        'user_id' => $this->user->id

                    ]);

                    if($teacher){

                        $school_year_model = SchoolYear::find($this->school_year);

                        $school_year_model->teachers()->attach($teacher->id);

                        $subject->teachers()->attach($teacher->id);

                        $this->user->update(['teacher_id' => $teacher->id]);

                    }
                } catch (Exception $e) {

                    $this->failed(null);

                    $message = "L'insertion de l'enseignant a échoué!";

                    TeacherCreatingOrUpdatingFailedEvent::dispatch($this->user, $message);

                    return;


            }
            elseif ($this->updating) {

                $this->teacher->update([
                    'name' => strtoupper($this->name),
                    'surname' => ucwords($this->surname),
                    'nationality' => $this->nationality,
                    'contacts' => $this->contacts,
                    'marital_status' => $this->marital_status

                ]);
            }
            elseif ($this->edit_subject) {

                $update = $this->teacher->update([
                    'level_id' => $this->level_id,
                ]);

                if($update){

                    $subject = $this->subject;

                    $this->old_subject->teachers()->detach($this->teacher->id);

                    $subject->teachers()->attach($this->teacher->id);
                    
                    if($this->old_subject->id !== $this->subject->id){

                        $classes = $this->teacher->getTeachersCurrentClasses(true);

                        foreach($classes as $classe_id => $data){

                            $classe = $data['classe'];

                            $cursus = $data['cursus'];

                            $canMarkedAsWorked = $data['asWorkedDuration'];

                            DB::transaction(function($e) use ($classe, $cursus, $canMarkedAsWorked){
                                try {
                                    try {
                                        if($cursus){

                                            $cursus->update(['end' => Carbon::now(), 'teacher_has_worked' => $canMarkedAsWorked]);
                                        }
                                        else{

                                            $message = "La mise à jour n'a pas été effective!";

                                            $this->failed(null);

                                            TeacherCreatingOrUpdatingFailedEvent::dispatch($this->user, $message);

                                            return;

                                            
                                        }
                                    } catch (Exception $ee) {

                                        $message = "Une erreure inconnue est survenue veuillez réessayer dans quelques secondes!";

                                        $this->failed(null);

                                        TeacherCreatingOrUpdatingFailedEvent::dispatch($this->user, $message);

                                        return ;

                                    }
                                } catch (Exception $e) {

                                    $message = "Une erreure inconnue est survenue veuillez réessayer dans quelques secondes!";

                                    $this->failed(null);

                                    TeacherCreatingOrUpdatingFailedEvent::dispatch($this->user, $message);

                                    return;

                                }
                            });
                        }

                    }
                }

            }
        });
    }
}
