<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ZtwenJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $a;

    public $b;

    public $c;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($a, $b, $c)
    {
        $this->a = $a;

        $this->b = $b;

        $this->c = $c;
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
        
        $this->som = rand(12222, 158) + $this->a + $this->b + $this->c;
    }
}
