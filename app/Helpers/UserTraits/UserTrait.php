<?php

namespace App\Helpers\UserTraits;

use App\Models\Product;
use App\Models\ShoppingBag;
use App\Models\MyNotifications;
use App\Models\SeenLikeProductSytem;

trait UserTrait{



   
    public function __reporteThisUser()
    {
        
    }


    public function __backToUserProfilRoute()
    {
        return redirect()->route('user_profil', ['id' => $this->id]);
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




    
}