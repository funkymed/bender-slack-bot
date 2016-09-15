<?php

namespace BenderBundle\Service;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class MeteoService
 * @package BenderBundle\Service
 */
class MeteoService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!meteo';

    /**
     * @var array
     */
    private $badAnswer = array(
      "Tu sais pas ou t'habite ?",
      "T'es sérieux ?",
      "Patate !",
      "Ok, je vois le genre...",
    );

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!meteo pays ville';
    }

    /**
     * @param $text
     * @return string
     */
    public function getMessage($text) {

        $commands = $this->getCommands($text);

        if(!isset($commands[0]))
            return $this->array_random($this->badAnswer)." Il faut me donner un pays";

        if($commands[0]=='help')
            return $this->getHelp();

        if(!isset($commands[1]))
            return $this->array_random($this->badAnswer)." Il faut me donner une ville";

        $country = UCFirst(strtolower($commands[0]));
        $city    = UCFirst(strtolower($commands[1]));
        $url = sprintf("http://api.openweathermap.org/data/2.5/weather?q=%s,%s&appid=%s",$country,$city,$this->getContainer()->getParameter('api_weather_key'));

        $res = $this->get_data($url);
        $weather = json_decode($res);
        if($weather->cod!="404"){
            $temp = round($weather->main->temp);

            return "à $city, $country il fait ".$temp."C° ".$weather->weather[0]->description;

        }else{
            return "Oups j'arrive pas à savoir la méteo, je dois être bourré";
        }
    }
}