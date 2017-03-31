<?php

/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-30
 * Time: 10:48 AM
 */
require_once "Lib/Weather.php";
use Cellk\Yahoo\Weather;

class WeatherFactory
{
    public static function getWeather(){
        return new Weather();
    }

}