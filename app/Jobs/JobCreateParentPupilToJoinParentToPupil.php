<?php

namespace App\Jobs;

use App\Models\ParentPupil;
use App\Models\ParentRequestToFollowPupil;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCreateParentPupilToJoinParentToPupil implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $parentRequestToFollowPupil;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        $this->parentRequestToFollowPupil = $parentRequestToFollowPupil;

    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $parentable = $this->parentRequestToFollowPupil->parentable;

        $pupil = $this->parentRequestToFollowPupil->pupil;

        $relation = $this->parentRequestToFollowPupil->relation;

        ParentPupil::create(['parentable_id' => $parentable->id, 'pupil_id' => $pupil->id, 'relation' => $relation]);
    }
}
