<?php

namespace App\Helpers\UserTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\MyNotifications;
use App\Models\Product;
use App\Models\SeenLikeProductSytem;
use App\Models\ShoppingBag;
use App\Notifications\SendTokenToBlockedUserForVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait UserTrait{

    use ModelQueryTrait;



   
    public function __reporteThisUser()
    {
        
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


    public function __likedThis($product_id)
    {
        $product = Product::find($product_id);
        if($product){
            $likes = $this->likes;
            if($likes->count() > 0){
                if(!in_array($product_id, $likes->pluck('product_id')->toArray())){
                    $like = SeenLikeProductSytem::create([
                        'user_id' => $this->id,
                        'product_id' => $product_id,
                    ]);
                    if($like){
                        return true;
                    }
                    return false;
                }
                return false;
            }
            else{
                $like = SeenLikeProductSytem::create([
                    'user_id' => $this->id,
                    'product_id' => $product_id,
                ]);
                if($like){
                    return true;
                }
                return false;
            }
        }
        else{
            return abort(403, "Votre requête ne peut aboutir");
        }
    }




    public function classeWasNotSecureColumn($classe_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear();
        if($secure_column){
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();
            return $req1 == 0;
        }
        else{
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->where('classe_id', $classe_id)->count();
            $req2 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->where('classe_id', $classe_id)->count();
            $req3 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('locked_classe', true)->where('classe_id', $classe_id)->count();
            $req4 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('closed_classe', true)->where('classe_id', $classe_id)->count();
            if($req1 == 0 && $req2 == 0 && $req3 == 0 && $req4 == 0){
                return true;
            }
            return false;

        }
    }



    public function classeWasNotSecureForTeacher($classe_id, $secure_column = null)
    {
        $school_year_model = $this->getSchoolYear();
        $teacher_id = $this->teacher->id;
        $teacher = $school_year_model->teachers()->where('teachers.id', teacher_id)->first();
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
        $school_year_model = $this->getSchoolYear();
        if($secure_column){
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();
            return $req1 == 0;
        }
        else{
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->where('classe_id', $classe_id)->count();
            $req2 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('closed_classe', true)->where('classe_id', $classe_id)->count();
            if($req1 == 0 && $req2 == 0){
                return true;
            }
            return false;

        }
    }


    public function classeWasNotLockedForTeacher($classe_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear();
        if($secure_column){
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();
            return $req1 == 0;
        }
        else{
            $req1 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->where('classe_id', $classe_id)->count();
            $req2 = $this->teacher->securities()->where('school_year_id', $school_year_model->id)->where('locked_classe', true)->where('classe_id', $classe_id)->count();
            if($req1 == 0 && $req2 == 0){
                return true;
            }
            return false;

        }
    }




    public function ensureThatTeacherCanAccessToClass($classe_id)
    {
        $user = $this;
        $school_year_model = $this->getSchoolYear();

        $not_secure1 = $this->classeWasNotClosedForTeacher($classe_id);
        $not_secure2 = $this->classeWasNotLockedForTeacher($classe_id);

        if($not_secure1 && $not_secure2){
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
    
}