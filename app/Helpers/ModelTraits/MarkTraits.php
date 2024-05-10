<?php



namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Illuminate\Support\Carbon;

trait MarkTraits{


    use ModelQueryTrait;

    public function ensureThatMarkUpdateNotDelayed()
    {
        $auth = auth()->user();

        if($auth->isAdminAs('master')){

            return true;
        }

        $updated = $this->updated_at;

        $to_times = Carbon::parse($updated)->timestamp;

        $now = Carbon::now()->timestamp;

        return $this->calc($to_times, $now) <= $this->getDelay() ? true : false;

        //$auth->isAdminAs('master') ? true : false

    }


    /**
     * This method is used to compute the duration of a mark since creation to now 
     * @param $timestamp|$now in seconds
     * @return time in hours
    */

    public function calc($timestamp, $now)
    {

        return abs($timestamp - $now) / (3600);
    }


}