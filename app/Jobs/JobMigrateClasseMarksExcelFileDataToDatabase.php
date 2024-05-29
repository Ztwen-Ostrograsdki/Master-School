<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\ClasseMarksExcelFile;
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

class JobMigrateClasseMarksExcelFileDataToDatabase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $school_year_model;

    public $file_name;

    public $semestre;

    public $extension = '.XLS';

    public $file_path;

    public $subject;

    public $user;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $extension, $file_name, $file_path, SchoolYear $school_year_model, $semestre, Subject $subject, User $user)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->extension = $extension;

        $this->semestre = $semestre;

        $this->file_name = $file_name;

        $this->file_path = $file_path;

        $this->subject = $subject;

        $this->user = $user;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = [
            'name' => $this->file_name,
            'extension' => $this->extension,
            'path' => $this->file_path, 
            'classe_id' => $this->classe->id, 
            'subject_id' => $this->subject->id, 
            'school_year_id' => $this->school_year_model->id, 
            'semestre' => $this->semestre, 
            'user_id' => $this->user->id
        ];

        ClasseMarksExcelFile::where('name', $this->file_name)->where('path', $this->file_path)->where('classe_id', $this->classe->id)->where('subject_id', $this->subject->id)->each(function($file){

            $old->delete();

        });

        ClasseMarksExcelFile::create($data);

    }
}
