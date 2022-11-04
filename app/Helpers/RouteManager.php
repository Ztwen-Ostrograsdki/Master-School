<?php
namespace App\Helpers;

use Illuminate\Support\Facades\URL;


class RouteManager{




    public static function hasUrl(string $url)
    {
        $part1 = array_reverse(explode(':', URL::full()));
        $part2 = array_flip(explode('/', $part1[0]));
        if($url){
            return array_key_exists($url, $part2);
        }

    }





}