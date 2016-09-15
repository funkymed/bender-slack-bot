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
        return '!meteo ville pays';
    }

    /**
     * @param $text
     * @return string
     */
    public function getMessage($text) {

        $commands = $this->getCommands($text);

        if($commands[0]=='help')
            return $this->getHelp();

        if(!isset($commands[0]))
            return $this->array_random($this->badAnswer)." Il faut me donner une ville";

        $city    = UCFirst(strtolower($commands[0]));

        if(isset($commands[1])){
            $country = UCFirst(strtolower($commands[1]));
            $url = sprintf("http://api.openweathermap.org/data/2.5/weather?q=%s,%s&appid=%s&units=metric&lang=fr",$city,$country,$this->getContainer()->getParameter('api_weather_key'));
        }else{
            $url = sprintf("http://api.openweathermap.org/data/2.5/weather?q=%s&appid=%s&units=metric&lang=fr",$city,$this->getContainer()->getParameter('api_weather_key'));
        }

        $res = $this->get_data($url);
        $weather = json_decode($res);
        if($weather->cod=="200"){
            $temp = round($weather->main->temp);
            if(isset($commands[1])) {
                return "à $city, $country il fait " . $temp . "C° " . $weather->weather[0]->description;
            }else{
                return "à $city il fait " . $temp . "C° " . $weather->weather[0]->description;
            }

        }else{
            return "Oups j'arrive pas à savoir la méteo, je dois être bourré";
        }
    }
}