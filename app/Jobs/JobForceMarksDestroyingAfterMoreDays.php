<?php

namespace App\Jobs;

use App\Models\Mark;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobForceMarksDestroyingAfterMoreDays implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mark;


    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Mark $mark)
    {
        $this->mark = $mark;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $mark = $this->mark;
        
        if($mark){

            if($mark->trashed()){

                $mark->forceDelete();

            }

        }
    }
}
