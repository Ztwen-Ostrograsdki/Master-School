<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserAdminKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobAdminSessionKeyDestroyer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $user;

    public $key;


    public function __construct(UserAdminKey $key, User $user)
    {
        $this->user = $user;

        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->user->__hasAdminKey()){

            $this->user->__destroyAdminKey();

        }
    }
}
