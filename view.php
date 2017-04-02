<?php
/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-17
 * Time: 11:25 PM
 */

require_once 'vendor/autoload.php';
require_once "Lib/WeatherFactory.php";


$strJson = '[{"city":"Vancouver","region":"BC"},{"city":"Honolulu","region":"HI"},{"city":"San Diego","region":"CA"},{"city":"Havana","region":"CH"}]';

$weather = WeatherFactory::yahooWeather();
$data = $weather->cityForecast(5, $strJson);
$weather->render('view', array('temperature' => $data));