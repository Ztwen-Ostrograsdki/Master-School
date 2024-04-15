<?php

namespace App\Helpers\UserTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\MyNotifications;
use App\Models\Parentable;
use App\Models\Product;
use App\Models\SeenLikeProductSytem;
use App\Models\ShoppingBag;
use App\Notifications\SendTokenToBlockedUserForVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait UserTrait{

    use ModelQueryTrait;



   
    public function __reporteThisUser()
    {
        
    }

    public function __canAccessToThisRoute($routeName = null, $url = null, $path = null)
    {
        $can = true;

        if($routeName){

            $can = !$this->__thisRouteIsLockedForThatUser($routeName);

            return $can;

        }

        if($url){

            $can = !$this->__thisRouteIsLockedForThatUser(null, $url);

            return $can;

        }


        if($path){

            $can = !$this->__thisRouteIsLockedForThatUser(null, null, $path);

            return $can;

        }
        else{

            return true;

        }

        return $can;

    }


    public function __backToUserProfilRoute()
    {
        return redirect()->route('user_profil', ['id' => $this->id]);
    }

    public function ___backToAdminRoute()
    {
        return redirect()->route('admin');
    }


    public function __getKeyNotification($getAndHide = false)
    {
        $notification = MyNotifications::where('user_id', $this->id)->where('target', 'Admin-Key')->first();
        $message = "Aucune clé n'a été généré!";
        if($notification){
            if(!$notification->hide){
                $message = $notification->content;
                if($getAndHide){
                    $notification->update(['hide' => true]);
                }
            }
            else{
                $message = "Désolé, vous n'y avez plus accès!";
            }
        }

        return $message;
    }

    public function __getAdvancedKeyNotification()
    {
        $notification = MyNotifications::where('user_id', $this->id)->where('target', 'Admin-Advanced-Key')->first();
        if($notification){
            return $notification->content;
        }
        return "Aucune clé n'a été généré!";
    }



    
    /**
     * Determine if a product was liked by the user
     *
     * @param int $product_id
     * @return bool
     */
    public function __alreadyLikedThis($product_id)
    {
        
    }



    public function classeWasNotSecureColumn($classe_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($secure_column){

            $req = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();

            return $req == 0 ? true : false;
        }
        else{
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->where('classe_id', $classe_id)->count();

            $req2 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->where('classe_id', $classe_id)->count();

           
            return ($req1 && $req2) ? true : false;

        }
    }



    public function classeWasNotSecureForTeacher($classe_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $teacher_id = $this->teacher->id;

        $teacher = $school_year_model->teachers()->where('teachers.id', $teacher_id)->first();

        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

        if($classe && $teacher){

            $teacher_classes = auth()->user()->teacher->getTeachersCurrentClasses();

            if(array_key_exists($classe->id, $teacher_classes)){

                if(!$classe->hasSecurities()){

                    if($classe->classeWasNotSecureColumn($teacher->id)){

                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
            else{
                return false;
            }
        }
        else{
            return false;

        }
    }

    public function classeWasNotClosedForTeacher($classe_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($secure_column){

            $req = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();

            return $req == 0 ? true : false;
        }
        else{

            $req = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->where('classe_id', $classe_id)->count();

            return $req == 0 ? true : false;

        }
    }


    public function classeWasNotLockedForTeacher($classe_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($secure_column){

            $req = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();

            return $req == 0 ? true : false;
        }
        else{

            $req = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->where('classe_id', $classe_id)->count();
           
            return $req == 0 ? true : false;

        }
    }




    public function ensureThatTeacherCanAccessToClass($classe_id)
    {
        $user = $this;

        $school_year_model = $this->getSchoolYear();

        $not_secure0 = $this->teacher;

        $not_secure3 = $this->teacher->getTeachersCurrentClasses(null, $school_year_model->id, $classe_id);

        $not_secure1 = $this->classeWasNotClosedForTeacher($classe_id);

        $not_secure2 = $this->classeWasNotLockedForTeacher($classe_id);

        if($not_secure0 && $not_secure1 && $not_secure2 && $not_secure3 || $this->isAdminAs('master')){

            return true;
        }

        return false;
    }


    /**
     * To generate a unlocken token for a blocked user
        @return objet (the user'model) or false
     */
    public function __generateUnlockedToken()
    {
        $key = Str::random(4);
        $key = 'abc123';
        $token =  Hash::make($key);

        $this->notify(new SendTokenToBlockedUserForVerification($key));

        if($this->update(['unlock_token' => $token])){
            return $this;
        }
        return false;
    }



    public function __unlockOrLockThisUser()
    {
        return $this->__blockerManager();
    }



    public function __blockerManager()
    {
        DB::transaction(function($e){
            try {
                if(!$this->blocked && !$this->locked){

                    $this->update(['locked' => true, 'blocked' => true, 'unlock_token' => null]);

                    if($this->lockedRequests){

                        $this->lockedRequests->delete();
                    }
                }
                else{

                    $this->update(['locked' => false, 'blocked' => false, 'unlock_token' => null]);

                    if($this->lockedRequests){

                        $this->lockedRequests->delete();
                    }
                }
                
            } catch (Exception $exceptError) {
                
                return false;
            }

        });

        DB::afterCommit(function(){
            return $this;
        });
    }


    public function getUsers($search = null, $blocked = false, $email_verified_token = null, $pseudo = null, $roles = [], $sexe = null, $teacher = null)
    {
        // where s : => pseudo - role - sexe - email_verified_token - blocked - teacher
        $users = [];

        if($search){


        }



        return $users;

    }


    /**
     * @return only ids
     */
    public function getUsersWithoutSearch($blocked = false, $email_verified_token = null, $pseudo = null, $roles = [], $sexe = null, $teacher = null)
    {
        $users = [];

        if($blocked){

            if($email_verified_token){

                if($pseudo){

                    if($roles){

                        if($sexe){

                            if($teacher){

                                //b e p r s t

                            }
                            else{

                                //b e p r s


                            }


                        }
                        elseif($teacher){

                            //b e p r t

                        }
                        else{

                            //b e p r

                        }

                    }
                    elseif($sexe){

                        if($teacher){

                            //b e p s t

                        }
                        else{

                            //b e p s

                        }

                    }
                    elseif($teacher){

                        //b e p t

                    }
                    else{

                        //b e p

                    }

                }
                elseif($roles){

                    if($sexe){

                        if($teacher){

                            //b e p s t

                        }
                        else{

                            //b e r s


                        }

                    }
                    elseif($teacher){

                        //b e r t

                    }
                    else{

                        //b e r


                    }

                }
                elseif($sexe){

                    if($teacher){

                        //b e s t


                    }
                    else{
                        //b e s

                    }

                }
                elseif($teacher){

                    //b e t

                }
                else{

                    //b e


                }

            }
            elseif($pseudo){

                if($roles){

                    if($sexe){

                        if($teacher){
                            //b p r s t

                        }
                        else{

                            //b p r s


                        }


                    }
                    elseif($teacher){
                        //b p r t

                    }
                    else{
                        //b p r

                    }


                }
                elseif($sexe){

                    if($teacher){
                        //b p s t


                    }
                    else{

                        //b p s


                    }

                }
                elseif($teacher){
                    //b p t

                }
                else{

                    //b p


                }

            }
            elseif($roles){

                if($sexe){

                    if($teacher){
                        //b r s t

                    }
                    else{

                        //b r s

                    }

                }
                elseif($teacher){
                    //b r t

                }
                else{
                    //b r s t

                }

            }
            elseif($sexe){

                if($teacher){

                    //b s t

                }
                else{
                    //b s

                }

            }
            elseif($teacher){
                //b t

            }
            else{

                //b


            }

        }
        else{

            if($email_verified_token){

                if($pseudo){


                }
                elseif($roles){

                    if($sexe){

                        if($teacher){
                            //e r s t

                        }
                        else{
                            //e r s

                        }

                    }
                    elseif($teacher){
                        //e r t


                    }
                    else{
                        //e r

                    }


                }
                elseif($sexe){

                    if($teacher){
                        //e s t


                    }
                    else{
                        //e s

                    }


                }
                elseif($teacher){
                    //e t


                }
                else{
                    //e


                }

            }
            elseif($pseudo){

                if($roles){

                    if($sexe){

                        if($teacher){
                            //p r s t

                        }
                        else{
                            //p r s

                        }

                    }
                    elseif($teacher){
                        //p r t


                    }
                    else{
                        //p r

                    }

                }
                elseif($sexe){

                    if($teacher){
                        //p s t

                    }
                    else{
                        //p s

                    }

                }
                elseif($teacher){
                    //p t


                }
                else{
                    //p

                }
            }
            elseif($roles){

                if($sexe){

                    if($teacher){
                        //r s t

                    }
                    else{
                        //r s

                    }


                }
                elseif($teacher){
                    //r t


                }
                else{
                    //r

                }

            }
            elseif($sexe){

                if($teacher){

                    //s t

                }
                else{
                    //s


                }

            }
            elseif($teacher){
                //t

            }
            else{
                //


            }

        }

        return $users;

    }


    public function parentable_creator($contacts, $job, $name, $residence)
    {
        return Parentable::create([

            'user_id' => $this->id,
            'name' => ucwords($name),
            'contacts' => str_replace(' ', '/', trim($contacts)),
            'job' => $job,
            'residence' => $residence,
            'key' => $this->parentable_password_generator()

        ]);
    }


    public function parentable_password_generator()
    {
        return Hash::make('0000E');
    }
    
}