<?php
/**
 * Created by PhpStorm.
 * User: kennytchu
 * Date: 2017-03-30
 * Time: 10:53 AM
 */

namespace Cellk\Yahoo;

interface YahooWeatherInterface
{

    /**
     *
     * @param string $name which correspond to the city's name
     * @param string $region which correspond to city's region
     *
     * @return string representation of api response
     *
     * @throws \Exception
     */
    public function apiWeatherCity($name = null, $region = null);

    /**
     *
     * @param string $yahooQuery
     */
    public function apiWeather($yahooQuery = null);
}
