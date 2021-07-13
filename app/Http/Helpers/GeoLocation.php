<?php

namespace App\Http\Helpers;

class GeoLocation
{
    public static function getLatLongQuery($latitude, $longitude)
    {
        return "3959 * acos(cos(radians(" . $latitude . ")) * cos(radians(latitude)) * cos(radians(longitude) - radians(" . $longitude . "))+ sin(radians(" .$latitude. ")) * sin(radians(latitude))) AS distance";
    }
}