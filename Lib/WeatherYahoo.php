<?php

/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-17
 * Time: 11:23 PM
 */

namespace Cellk\Weather;

use GuzzleHttp\Client as ClientHttp;
use GuzzleHttp\Exception\RequestException;
include_once("WeatherInterface.php");

class WeatherYahoo implements WeatherInterface
{
    /*
     * Temperature in Celsius
     */
    const URL_BASE = 'https://query.yahooapis.com/v1/public/yql?format=json&q=';
    const CITY_NAME_QUERY = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="%s,%s") and u="c"';

    /**
     *  Twig
     */
    protected $twig;

    /**
     * @var Response from the API
     */
    protected $response;

    /**
     * @var Number of days to get weather's forecast
     */
    protected $nbDays;

    /**
     * @var array of cities to get info from API
     */
    protected $cities;

    /**
     * @var array cities Weather from API
     */
    protected $citiesWeather = array();

    /**
     * @var string request to API
     */
    protected $yahooQuery;


    public function __construct()
    {

        $loader = new \Twig_Loader_Filesystem('./views/');
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => false,
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }


    //Generate the view using Twig template
    public function render($view, $data)
    {
        $template = $this->twig->load($view);
        echo $template->render($data);
    }


    /**
     * {@inheritdoc}
     */
    public function apiWeatherCity($city = null, $region = null)
    {
        if ($city === null && $this->city === null) {
            throw new \Exception('Please provide a city\'s name', 400);
        }
        if ($city !== null) {
            $this->city = $city;
        }

        $this->yahooQuery = self::URL_BASE . urlencode(sprintf(self::CITY_NAME_QUERY, $this->city, $region));
        return $this->apiWeather();
    }

    /**
     * {@inheritdoc}
     */
    public function apiWeather($yahooQuery = null)
    {
        if ($yahooQuery === null && empty($this->yahooQuery)) {
            throw new \Exception('Please provide a YQL request', 400);
        }
        if ($yahooQuery !== null) {
            $this->yahooQuery = $yahooQuery;
        }

        try {
            $client = new ClientHttp();
            $response = $client->get($this->yahooQuery)->json();

            if (isset($response['query']['results']['channel']['item'])) {
                if ($this->nbDays === 1) {
                    $this->response = $response['query']['results']['channel']['item']['condition'];
                } else {
                    $this->response = $response['query']['results']['channel']['item']['forecast'];
                }
            } else {
                $this->response = false;
            }

        } catch (RequestException $e) {
            throw new RequestException("Error happened, please check your request url whether it's correct.", 400);
        }

        return $this->response;
    }


    /*
     * Return today's weather for every city.
     *
     *
     * @return array | false
     */
    public function weatherToday($json)
    {
        $this->setCities($json);
        $this->nbDays = 1;

        $list = $this->cities;
        if (!empty($list)) {
            $nbCities = count($list);
            for ($i = 0; $i < $nbCities; $i++) {
                $this->apiWeatherCity($list[$i]->city, $list[$i]->region);
                $data = $this->getCommonData($this->response);
                $data = array_merge($data, array(
                    'location' => array(
                        'city' => $list[$i]->city,
                        'region' => $list[$i]->region,
                    ),
                    'temp' => $this->response['temp']
                ));
                array_push($this->citiesWeather, $data);
            }
            return $this->citiesWeather;
        }
        return false;

    }

    /**
     * Return a forecast of the city
     *
     * @param int $nbDays How many days forecast
     * @return array | false
     */
    public function cityForecast($nbDays = 2, $json)
    {
        $this->setCities($json);
        $this->nbDays = (int)$nbDays;
        $list = $this->cities;

        if ($this->nbDays >= 1) {
            $this->nbDays--;
        }

        if (!empty($list)) {
            $nbCities = count($list);

            //Loop for every city
            for ($i = 0; $i < $nbCities; $i++) {
                $this->apiWeatherCity($list[$i]->city, $list[$i]->region);
                $this->citiesWeather[$i] = array(
                    'location' => array(
                        'city'=> $list[$i]->city,
                        'region' => $list[$i]->region,
                        'forecast' => array()
                    )
                );

                // loop to get temperature for x days
                for ($j = 0; $j <= $this->nbDays; $j++) {
                    $data = $this->getCommonData($this->response[$j]);
                    $data = array_merge($data, array(
                        'low' => $this->response[$j]['low'],
                        'high' => $this->response[$j]['high']
                    ));
                    array_push($this->citiesWeather[$i]['location']['forecast'], $data);
                }
            }
            return $this->citiesWeather;
        }

        return false;

    }

    /*
     * Decode the Json into an array
     *
     * @param $string Json
     * @throws Exception
    */
    private function setCities($string)
    {
        try{
            $cities = @json_decode($string);
            if (is_string($string) && (json_last_error() === JSON_ERROR_NONE)) {
                $this->cities = @$cities;
            }else{
                throw new \InvalidArgumentException('The Json format is not correct ');
            }
        }catch (\InvalidArgumentException $e){
            echo $e->getMessage();
        }

    }


    /**
     *
     *
     * @param array $node Path
     * @return array
     */
    private function getCommonData($node)
    {
        $dateFormat = 'F d, Y';
        return array(
            'date' => (isset($node["date"]) ? date($dateFormat, strtotime($node["date"])) : ""),
            'description' => (isset($node["text"])) ? $node["text"] : "",
            'img' => $this->getImgCode($node)
        );
    }


    /*
     * Return the weather's code with the link to the icon
     *
     * @param array $nodeForecast Path to the key 'forecast
     * @return array | false
     */
    private function getImgCode($nodeForecast = null)
    {
        if (!isset($nodeForecast['code'])) {
            return false;
        }

        return array(
            'code' => $nodeForecast['code'],
            'gif' => 'http://l.yimg.com/a/i/us/we/52/' . $nodeForecast['code'] . '.gif'
        );
    }


}