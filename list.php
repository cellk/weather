<?php
/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-17
 * Time: 11:42 PM
 */

require_once "vendor/autoload.php";
require_once "Lib/WeatherFactory.php";

/*$list = array(
    array(
        "city" => "Vancouver",
        "region" => "BC"),
    array(
        "city" => "Honolulu",
        "region" => "HI"),
    array(
        "city" => "San Diego",
        "region" => "CA"),
    array(
        "city" => "Havana",
        "region" => "CH"));

$strJson = json_encode($list);*/
$strJson = '[{"city":"Vancouver","region":"BC"},{"city":"Honolulu","region":"HI"},{"city":"San Diego","region":"CA"},{"city":"Havana","region":"CH"}]';

$weather = WeatherFactory::getWeather();
$data = $weather->weatherToday($strJson);
$weather->render('list', array('temperature' => $data));