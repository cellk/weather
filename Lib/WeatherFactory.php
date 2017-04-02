<?php

/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-30
 * Time: 10:48 AM
 */
require_once "Lib/WeatherYahoo.php";
require_once "Lib/WeatherApixu.php";
use Cellk\Weather\WeatherYahoo;
use Cellk\Weather\WeatherApixu;

class WeatherFactory
{
    public static function yahooWeather(){
        return new WeatherYahoo();
    }

    public static function apixuWeather(){
        return new WeatherApixu();
    }

}