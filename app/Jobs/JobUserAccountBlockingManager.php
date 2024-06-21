<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class JobUserAccountBlockingManager implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $force_blocking = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $force_blocking = false)
    {
        $this->user = $user;

        $this->force_blocking = $force_blocking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->force_blocking){

            $this->user->update(['locked' => true, 'blocked' => true, 'unlock_token' => null]);

            Auth::guard('web')->logout();

            Session::flush();

        }

        else{

            if($this->user->isBlocked()){

                $this->user->__unlockOrLockThisUser();

            }
            else{

                Auth::guard('web')->logout();

                Session::flush();

            }

        }

        


    }
}
