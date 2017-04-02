<?php

/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-30
 * Time: 10:48 AM
 */
require_once "Lib/WeatherYahoo.php";
use Cellk\Weather\WeatherYahoo;

class WeatherFactory
{
    public static function yahooWeather(){
        return new WeatherYahoo();
    }

}