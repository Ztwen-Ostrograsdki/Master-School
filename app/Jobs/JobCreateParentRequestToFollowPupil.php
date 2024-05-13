<?php

namespace App\Jobs;

use App\Models\Parentable;
use App\Models\Pupil;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCreateParentRequestToFollowPupil implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $parentable;

    public $pupil;

    public $relation;

    public $authorized;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Parentable $parentable, Pupil $pupil, $relation, bool $authorized = false)
    {
        $this->parentable = $parentable;

        $this->pupil = $pupil;

        $this->relation = $relation;

        $this->authorized = $authorized;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->parentable->requestToFollowThisPupil($this->pupil->id, $this->relation, false);
    }
}
