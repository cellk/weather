## Synopsis

A very simple web application that utilizes Multiple Weather APIs to construct a list view of cities and their weather forecast information

## Example - Using Yahoo API

```php
require_once 'vendor/autoload.php';
require_once 'Lib/WeatherFactory.php';

// cities in Json
$strJson = '[{"city":"Vancouver","region":"BC"},{"city":"Honolulu","region":"HI"},{"city":"San Diego","region":"CA"},{"city":"Havana","region":"CH"}]';

/* 
 * weatherToday : Return today's temperature for each city
 * cityForecast(5, $strJson) : Return 5 days forecast for each city
 */
$weather = WeatherFactory::yahooWeather();
$data = $weather->weatherToday($strJson);


// Rendering with twig
$view = 'list';
$weather->render($view, array('temperature' => $data));
```
## Example - Using Apixu API
```php
require_once 'vendor/autoload.php';
require_once 'Lib/WeatherFactory.php';

/* 
 * cityForecast : Return Vancouver today's forecast from 8am to 3pm
 * weatherToday('API_KEY', 'Vancouver') : Return today's weather in Vancouver
 */
$weather = WeatherFactory::apixuWeather();
$data = $weather->cityForecast('API_KEY', 'Vancouver', 8, 15);


// Rendering with twig
$view = 'list';
$weather->render($view, array('temperature' => $data));
```

## Installation

PHP 5.4+

git clone https://github.com/cellk/weather.git

php composer.phar install

## API and vendor References

Twig : template engine for PHP

Guzzle : PHP HTTP client

Weather API : Yahoo

