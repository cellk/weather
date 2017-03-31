<?php

/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-30
 * Time: 7:48 PM
 */
namespace Cellk\Yahoo\Tests;
require_once "./Lib/Weather.php";

use PHPUnit\Framework\TestCase;
use Cellk\Yahoo\Weather;

class WeatherTest extends TestCase
{

    public function testApiWeatherCity(){
        $city = 'Montreal';
        $region = 'QC';

        $weather = new Weather();
        $response = $weather->apiWeatherCity($city, $region);
        $this->assertSame(is_array($response), true);
    }

    public function testApiWeather(){
        $yahooQuery = "https://query.yahooapis.com/v1/public/yql?format=json&q=select * from weather.forecast where woeid in (select woeid from geo.places(1) where text='Vancouver,BC') and u='c'";
        $weather = new Weather();
        $response = $weather->apiWeather($yahooQuery);
        $this->assertSame(is_array($response), true);
    }

    public function testWeatherToday(){
        $data = array(
            array(
                'city' => 'Montreal',
                'region' => 'QC'
            )
        );
        $weather = new Weather();
        $response = $weather->weatherToday(json_encode($data));
        $this->assertSame(is_array($response), true);

    }

    public function testWeatherTodayException(){
        $this->expectExceptionMessage("The Json format is not correct");
        $weather = new Weather();
        $weather->weatherToday("Not a valid json string");
    }

}