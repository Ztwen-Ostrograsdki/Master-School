<?php



namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait MarkTraits{


    use ModelQueryTrait;


    public function validateUpdatingValue($new_value, User $user, $others_data = [])
    {
        DB::transaction(function($e) use($new_value, $user, $others_data){

            if($user->isAdminAs('master-ztwen')){

                if($others_data){ 

                    $this->update(['value' => $new_value]);

                    return $this->update($others_data);

                }

                return $this->update(['value' => $new_value]);
                
            }
            else{


                if($others_data){ 

                    $this->forceFill(['editing_value' => $new_value, 'editor' => $user->id, 'updating' => true])->save();

                    return $this->update($others_data);


                }

                return $this->forceFill(['editing_value' => $new_value, 'editor' => $user->id, 'updating' => true])->save();

            }

        });
    }


    public function ensureThatMarkUpdateNotDelayed($delay = null)
    {
        $auth = auth()->user();

        // if($auth->isAdminAs('master')){

        //     return true;
        // }

        $updated = $this->updated_at;

        $to_times = Carbon::parse($updated)->timestamp;

        $now = Carbon::now()->timestamp;

        $delay_duration = $this->getDelay();

        if($delay){

            $delay_duration = $delay;

        }

        return $this->calc($to_times, $now) <= $delay_duration ? true : false;

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