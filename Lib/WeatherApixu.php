<?php
/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-04-01
 * Time: 5:09 PM
 */

namespace Cellk\Weather;

use GuzzleHttp\Client as ClientHttp;
use GuzzleHttp\Exception\RequestException;

include_once("WeatherInterface.php");

class WeatherApixu implements WeatherInterface
{
    const URL_CURRENT = "https://api.apixu.com/v1/current.json?";
    const URL_FORECAST = "https://api.apixu.com/v1/forecast.json?";

    protected $response;

    protected $city;

    protected $apixuQuery;

    protected $apiKey;

    protected $cityWeather;

    protected $forecast = false;

    protected $dateFormat = 'F d, Y g:i a';

    function __construct()
    {

    }

    /**
     * @inheritDoc
     */
    public function apiWeatherCity($city = null, $region = null)
    {
        if ($city === null && $this->city === null) {
            throw new \Exception('Please provide a city\'s name', 400);
        }

        if (empty($this->apiKey)) {
            throw new \Exception('Please provide an API KEY', 400);
        }


        if ($city !== null) {
            $this->city = $city;
        }

        if ($this->forecast === false)
            $this->apixuQuery = self::URL_CURRENT . 'key=' . $this->apiKey . '&q=' . $this->city;
        else
            $this->apixuQuery = self::URL_FORECAST . 'key=' . $this->apiKey . '&q=' . $this->city;

        return $this->apiWeather();
    }

    /**
     * @inheritDoc
     */
    public function apiWeather($apixuQuery = null)
    {
        if ($apixuQuery === null && empty($this->apixuQuery)) {
            throw new \Exception('Please provide an apixu request', 400);
        }
        if ($apixuQuery !== null) {
            $this->apixuQuery = $apixuQuery;
        }

        try {
            $client = new ClientHttp();
            $response = $client->get($this->apixuQuery)->json();

            $this->response = $response;

        } catch (RequestException $e) {
            throw new RequestException("Error happened, please check your request url whether it's correct.", 400);
        }

        return $this->response;
    }

    public function weatherToday($key, $city)
    {
        if (empty($key)) {
            throw new \Exception('Please provide an API Key', 400);
        }

        $this->apiKey = $key;


        $this->apiWeatherCity($city);
        $data = array(
            'city' => $this->response['location']['name'],
            'region' => $this->response['location']['region'],
            'date' => date($this->dateFormat, strtotime($this->response['current']['last_updated'])),
        );

        $node = $this->response['current'];
        $this->cityWeather = array_merge($data, $this->getCommonData($node));

        return $this->cityWeather;
    }

    public function cityForecast($key, $city, $startHour = 0 , $endHour = 23)
    {
        if((int)$startHour >= (int)$endHour){
            throw new \Exception('The start hour must be inferior to the last hour', 400);
        }

        if (empty($key)) {
            throw new \Exception('Please provide an API Key', 400);
        }

        $this->apiKey = $key;
        $this->forecast = true;

        $this->apiWeatherCity($city);
        $data = array(
            'city' => $this->response['location']['name'],
            'region' => $this->response['location']['region'],
            'date' => date($this->dateFormat, strtotime($this->response['current']['last_updated'])),
        );

        $forecast = array();
        for ($j = $startHour; $j <= $endHour; $j++) {
            $node = $this->response['forecast']['forecastday'][0]['hour'][$j];
            array_push($forecast, $this->getCommonData($node));
        }
        $this->cityWeather = array_merge($data, $forecast);

        return $this->cityWeather;
    }

    /**
     *
     *
     * @param array $node Path
     * @return array
     */
    private function getCommonData($node)
    {
        $data =  array(
            'celsius' => $node['temp_c'],
            'fahrenheit' => $node['temp_f'],
            'description' => $node['condition']['text'],
            'img' => $node['condition']['icon']
        );

        if($this->forecast){
            $data = array_merge($data,array(
                'time' => $node['time'],
                'rain' => ((int)$node['will_it_rain'])? 'Yes' : 'No',
                'snow' => ((int)$node['will_it_snow'])? 'Yes' : 'No',
                'wind'=> $node['wind_dir']
            ));
        }

        return $data;

    }


}