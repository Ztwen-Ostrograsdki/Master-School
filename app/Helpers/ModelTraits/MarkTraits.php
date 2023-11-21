<?php



namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Illuminate\Support\Carbon;

trait MarkTraits{


    use ModelQueryTrait;

    public function ensureThatMarkUpdateNotDelayed()
    {
        $auth = auth()->user();

        $updated = $this->updated_at;

        $to_times = Carbon::parse($updated)->timestamp;

        $now = Carbon::now()->timestamp;

        return $this->calc($to_times, $now) <= $this->getDelay() ? true : false;

        //$auth->isAdminAs('master') ? true : false

    }


    /**
     * @param $timestamp|$now in seconds
    */

    public function calc($timestamp, $now)
    {

        return abs($timestamp - $now) / (3600);
    }


}