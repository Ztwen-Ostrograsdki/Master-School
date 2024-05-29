<?php

namespace App\Jobs;

use App\Models\Mark;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobUpdatingPupilsMarksManager implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mark_editor;

    public $mark;

    public $new_value = 0;

    public $others_data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Mark $mark, User $mark_editor, $new_value, $others_data = [])
    {
        $this->mark_editor = $mark_editor;

        $this->mark = $mark;

        $this->new_value = $new_value;

        $this->others_data = $others_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->batch()->cancelled()){

            return;

        }

        return $this->mark->validateUpdatingValue($this->new_value, $this->updater, $this->others_data);
    }


}
