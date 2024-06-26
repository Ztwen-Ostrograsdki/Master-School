<?php

namespace App\Jobs;

use App\Models\Parentable;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobSentParentKeyToParentableUser implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $key;

    public $parentable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Parentable $parentable, string $key)
    {
        $this->parentable = $parentable;

        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->parentable->user;

        $user->sendParentableKeyNotification($this->key);
    }
}
