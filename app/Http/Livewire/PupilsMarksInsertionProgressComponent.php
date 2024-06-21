<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PupilsMarksInsertionProgressComponent extends Component
{
    public $classe_id;

    public $progress_status;

    protected $listeners = ['UpdatePupilsMarksInsertionProgressLiveEvent' => 'checkProgress'];

    public function render()
    {
        $failed_jobs = $this->getFailsJobs();

        $progress_status = $this->progress_status;

        $taking = 1;

        $classe = Classe::find($this->classe_id);

        $progress = 0;

        $marks_insertion_batches = [];

        $marks_deletion_batches = [];

        $db_updating_marks_batches = [];

        $trying_to_update_pupil_mark_batches = [];

        // $marks_insertion_tasks = [];
        // $trying_to_update_pupil_mark_tasks = [];
        // $marks_deletion_tasks = [];
        // $db_updating_marks = [];

        $marks_insertion_tasks = DB::table('job_batches')->where('total_jobs', '>', 0)->where('name', "marks_insertion")->orderBy('created_at', 'desc')->take($taking)->get();

        $trying_to_update_pupil_mark_tasks = DB::table('job_batches')->where('total_jobs', '>', 0)->where('name', "trying_to_update_pupil_mark")->orderBy('created_at', 'desc')->take($taking)->get();

        $marks_deletion_tasks = DB::table('job_batches')->where('total_jobs', '>', 0)->where('name', "marks_deletion")->orderBy('created_at', 'desc')->take($taking)->get();

        $db_updating_marks = DB::table('job_batches')->where('total_jobs', '>', 0)->where('name', "updating_marks_into_database")->orderBy('created_at', 'desc')->take($taking)->get();


        foreach($db_updating_marks as $task1){

            $db_updating_marks_batches[] = Bus::findBatch($task1->id);

        }

        foreach($marks_insertion_tasks as $task2){

            $marks_insertion_batches[] = Bus::findBatch($task2->id);

        }

        foreach($marks_deletion_tasks as $task3){

            $marks_deletion_batches[] = Bus::findBatch($task3->id);

        }

        foreach($trying_to_update_pupil_mark_tasks as $task4){

            $trying_to_update_pupil_mark_batches[] = Bus::findBatch($task4->id);

        }

        return view('livewire.pupils-marks-insertion-progress-component', compact('marks_insertion_batches', 'marks_deletion_batches', 'db_updating_marks_batches', 'trying_to_update_pupil_mark_batches', 'classe', 'failed_jobs'));
    }

    // public function getListeners()
    // {
    //     $userID = auth()->user()->id;

    //     return ["echo-private:user.{$userID}, UpdatePupilsMarksInsertionProgressEvent" => "checkProgress"];
    // }

    public function deleteXBatch($batch_id)
    {
        $batch = Bus::findBatch($batch_id);


        if($batch){

            $batch->delete();

        }
    }

    public function cancelXBatch($batch_id)
    {
        $batch = Bus::findBatch($batch_id);

        if($batch){

            $batch->cancel();

        }
    }
    
    public function retryXBatch($batch_id)
    {
        $batch = Bus::findBatch($batch_id);

        if($batch && $batch->hasFailures()){

            DB::table('job_batches')->where('id', $batch_id)->update(['failed_jobs' => 0, 'failed_job_ids' => '[]' ]);

            return Artisan::call('queue:retry-batch', ['id' => $batch_id]);

        }

        
    }

    public function checkProgress($status = 100)
    {
        $this->progress_status = 100;
    }


    public function deleteFailsJobs()
    {
        return Artisan::call('queue:flush');
    }


    public function retryFailsJobs()
    {
        // $last = DB::table('failed_jobs')->whereNotNull('failed_at')->orderBy('created_at', 'desc')->first();

        return Artisan::call('queue:retry');
    }

    public function getFailsJobs()
    {
        return DB::table('failed_jobs')->whereNotNull('failed_at')->count();
    }
}
