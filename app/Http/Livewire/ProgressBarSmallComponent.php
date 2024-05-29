<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProgressBarSmallComponent extends Component
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

        $no_batching = true;

        $no_batching_insert = true;

        $no_batching_del = true;

        $no_batching_up_db = true;

        $marks_insertion_tasks = DB::table('job_batches')->where('total_jobs', '>', 0)->where('name', "marks_insertion")->orderBy('created_at', 'desc')->take($taking)->get();

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

        if($marks_deletion_batches || $marks_insertion_batches || $db_updating_marks_batches){

            if($marks_deletion_batches){

                $batch_del = $marks_deletion_batches[0];

                if(!$batch_del->finished()){

                    $no_batching_del = false;

                }

            }
            if($marks_insertion_batches){

                $batch_insert = $marks_insertion_batches[0];

                if(!$batch_insert->finished()){

                    $no_batching_insert = false;

                }

            }
            if($db_updating_marks_batches){

                $batch_up_db = $db_updating_marks_batches[0];

                if(!$batch_up_db->finished()){

                    $no_batching_up_db = false;

                }

            }

            if($no_batching_up_db == false || $no_batching_insert == false || $no_batching_del == false){

                $no_batching = false;

            }


        }

        return view('livewire.progress-bar-small-component', compact('marks_insertion_batches', 'marks_deletion_batches', 'db_updating_marks_batches', 'classe', 'failed_jobs', 'no_batching'));
    }

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
        return Artisan::call('queue:retry');
    }

    public function getFailsJobs()
    {
        return DB::table('failed_jobs')->whereNotNull('failed_at')->count();
    }
}

