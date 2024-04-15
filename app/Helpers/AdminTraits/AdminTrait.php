<?php

namespace App\Helpers\AdminTraits;

use App\Helpers\DateFormattor;
use App\Models\ClassesSecurity;
use App\Models\MyNotifications;
use App\Models\SchoolHistory;
use App\Models\UserAdminKey;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


/**
 * Manage all about the admins
 */
trait AdminTrait{


    use DateFormattor;



    /**
     * Use to regenerate or create a new admin into session who want to connect to the an amin route/dashboard
     * 
     * @return void
     */
    public function __regenerateAdminSession()
    {
        session()->put('admin-' . $this->id, $this->id);
    }


    /**
     *Deterline if a user with admin or master role have been already into session before access to admin routes
        *
        * @return bool
        */
    public function __hasAdminAuthorization()
    {
        if(session()->has('admin-' . $this->id) && session('admin-' . $this->id) == $this->id){
            
            $this->__regenerateAdminSession();
            
            return true;
        }

        return false;
    }


    /**
     * Use to destroy an session 
     *
     * @return void
     */
    public function __hydrateAdminSession()
    {
        session()->forget('admin-' . $this->id);
    }

    
    /**
     * Use to destroy all session data
     *
     * @return void
     */
    public function __destroyAdminSession()
    {
        session()->flush();
    }

     /**
     * This method is used to send the weak key to an admin after the key has been generated
     *
     * @param string $key
     * @return bool
     */
    public function __sendKey($key)
    {
        $make = MyNotifications::create([
            'content' => $key . " #Bienvenu(e) sur la plateforme. Nous vous envoyons la clé de connexion à la page d'administration. ",
            'user_id' =>  auth()->user()->id,
            'target' => "Admin-Key",
            'target_id' => null
        ]);

        if($make){

            return true;
        }
        else{

            return false;
        }
    }

    /**
     * This method is used to send the adavanced or strong key to an admin after the key has been generated
     *
     * @param string $key
     * @return bool
     */
    public function __sendAdvancedKey($key)
    {
        $make = MyNotifications::create([
            'content' => $key . " #Salut! Je vous envoie la clé des requêtes irreversibles dans la page d'administration." ,
            'user_id' =>  auth()->user()->id,
            'target' => "Admin-Advanced-Key",
            'target_id' => null
        ]);
        if($make){
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Use to generate a weak key
     *
     * @return void
     */
    public function __generateAdminKey()
    {
        $key = Str::random(4);

        if($this->hasAdminKey()){

            $this->__destroyWeakKeys();

            $this->__refreshNotifications();
        }

        $make = UserAdminKey::create([
            'user_id' => $this->id,
            'key' =>  Hash::make($key)
        ]);

        if($make){

            $this->__destroyStrongKeys();

            $this->__refreshNotifications();

            return $this->__sendKey($key);
        }

        return false;
    }

    /**
     * Use to generate a strong key
     *
     * @return bool
     */
    public function __generateAdvancedRequestKey()
    {
        $key = Str::random(4);
        
        if($this->hasAdminAdvancedKey()){
            
            $this->__destroyStrongKeys();
            
            $this->__refreshNotifications();
        }
        
        $make = UserAdminKey::create([
            'user_id' => $this->id,
            'key' =>  Hash::make($key)
        ]);
        
        $make->forceFill(['advanced' => true])->save();
        
        if($make){
            
            $this->__destroyWeakKeys();
            
            $this->__refreshNotifications();
            
            return $this->__sendAdvancedKey($key);
        }
        return false;
    }
    /**
     * Use to regenerate a strong key
     *
     * @return bool
     */
    public function __regenerateAdvancedRequestKey()
    {
        return $this->__generateAdvancedRequestKey();
    }


    /**
     * Use to regenerate a weak key
     *
     * @return void
     */
    public function __regenerateAdminKey()
    {
        return $this->__generateAdminKey();
    }


    /**
     * Use to destroy the current admin key and flush a session authentication
     * The admin can't access to the admin 
     *
     * @return void
     */
    public function __destroyAdminKey()
    {
        if($this->hasAdminKey()){

            $this->userAdminKey->delete();

            $this->__hydrateAdminSession();

            $this->__refreshNotifications();
        }
        

        // $this->__backToUserProfilRoute();
    }


    
    /**
     * Use to destroy all admin keys that aren't avanced keys
     *
     * @return void
     */
    public function __destroyWeakKeys()
    {
        $weak_keys = UserAdminKey::where('user_id', $this->id)->where('advanced', false);
        if($weak_keys->get()->count() > 0){
            $weak_keys->delete();
        }
    }


    /**
     * Use to destroy all advanced adamin keys
     *
     * @return void
     */
    public function __destroyStrongKeys()
    {
        $strong_keys = UserAdminKey::where('user_id', $this->id)->where('advanced', true);

        if($strong_keys->get()->count() > 0){

            $strong_keys->delete();
        }
    }

    /**
     * Determine if an admin has a weak admin key
     *
     * @return boolean
     */
    public function hasAdminKey()
    {
        $key = $this->userAdminKey;

        if($key){
            return true;
        }
        return false;
    }  

      /**
     * Determine if an admin has a weak admin key
     *
     * @return boolean
     */
    public function __hasAdminKey()
    {
        $key = $this->userAdminKey;

        if($key){
            return true;
        }
        return false;
    }

    /**
     * Determine if an admin has a strong admin key or an advanced admin key
     *
     * @return boolean
     */
    public function hasAdminAdvancedKey()
    {
        $key = $this->userAdminKey;

        if($key && $key->advanced){
            return true;
        }
        return false;
    }

    /**
     * Determine if an admin has a strong admin key or an advanced admin key
     *
     * @return boolean
     */
    public function __hasAdminAdvancedKey()
    {
        $key = $this->userAdminKey;

        if($key && $key->advanced){
            return true;
        }
        return false;
    }


    /**
     * Use to refresh all notification about the admin key: strong and weak
     *
     * @return void
     */
    public function __refreshNotifications()
    {
        $notifications = MyNotifications::where('user_id', $this->id)->where('target', 'Admin-Key')->orWhere('target', 'Admin-Advanced-Key');

        if($notifications->get()->count() > 0){

            return $notifications->delete();
        }
        
    }


    public function __destroyClasseSecuritiesKeyExpired(array $keys_id = [])
    {

        if($keys_id !== []){

            $occurence = 0;

            $keys = ClassesSecurity::whereIn('id', $keys_id)->get();

            if(count($keys) > 0){

                DB::transaction(function($e) use ($keys, $occurence){

                    foreach($keys as $key){

                        $duration = $key->duration; // in hours

                        $initial_date = $key->updated_at;

                        $start = Carbon::parse($initial_date)->timestamp;


                        $diff_in_seconds = $this->__getTimestampInSecondsBetweenDates($start);


                        $diff_in_hours = $diff_in_seconds / 3600;

                        if($diff_in_hours >= $duration){

                            $key->delete();

                            $occurence++;

                        }

                    }


                });

                DB::afterCommit(function() use ($occurence){

                    $this->emit('ClasseSecuritiesWasDelete', $occurence);

                });


            }


        }


    }


    public function __createHistory($data)
    {
        if($data){

            $table = $data['table'];
            
            $model_id = $data['model_id'];
            
            $content = $data['content'];
            
            $description = $data['description'];
            
            $school_year_id = $data['school_year_id'];
            
            $visibility = $data['visibility'];

            $make = SchoolHistory::create([
                'table' => $table,
                'model_id' => $model_id,
                'content' => $content,
                'description' => $description,
                'school_year_id' => $school_year_id,
                'visibility' => $visibility,
            ]);

            return $make;

        }


    }

}