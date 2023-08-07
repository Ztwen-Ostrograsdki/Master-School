<?php

namespace App\Jobs;

use App\Models\ClassesSecurity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobDeleteClasseSecurityAfterExpired implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $security;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ClassesSecurity $security)
    {
        $this->security = $security;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->security){
            
            $this->security->delete();
        }
    }
}
