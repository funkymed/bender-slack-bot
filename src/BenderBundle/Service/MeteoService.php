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
     * @param $text
     * @return array|string
     */
    public function getMessage($text)
    {
        $answer = $this->checkAnswer($text);
        return $answer ? $this->getAnswer($answer) : "";
    }

    /**
     * @param $text
     * @return array|string
     */
    public function checkAnswer($text)
    {

        $commands = $this->getCommands($text);

        if ($commands[0] == 'help')
            return $this->getHelp();

        if (!isset($commands[0]))
            return $this->array_random($this->badAnswer) . " Il faut me donner une ville";

        $city = UCFirst(strtolower($commands[0]));

        if (isset($commands[1])) {
            $country = UCFirst(strtolower($commands[1]));
            $url = sprintf("http://api.openweathermap.org/data/2.5/weather?q=%s,%s&appid=%s&units=metric&lang=fr", $city, $country, $this->getContainer()->getParameter('api_weather_key'));
        } else {
            $url = sprintf("http://api.openweathermap.org/data/2.5/weather?q=%s&appid=%s&units=metric&lang=fr", $city, $this->getContainer()->getParameter('api_weather_key'));
        }

        $res = $this->get_page_content($url);
        $weather = json_decode($res);
        if ($weather->cod == "200") {
            $temp = round($weather->main->temp);
            if (isset($commands[1])) {
                return "à $city, $country il fait " . $temp . "C° " . $weather->weather[0]->description;
            } else {
                return "à $city il fait " . $temp . "C° " . $weather->weather[0]->description;
            }

        } else {
            return "Oups j'arrive pas à savoir la méteo, je dois être bourré";
        }
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!meteo ville pays';
    }

    protected function getAnswer($message)
    {
        if (is_array($message))
            $message = implode("\n", $message);

        $date = new \DateTime();
        return [
            "attachments" => [
                [
                    "title" => "Météo",
                    "color" => "#FF7A09",
                    "footer" => "Open weather map",
                    "footer_icon" => $this->getMediaUrl("/bundles/bender/icons/openweather.png"),
                    "title_link" => "http://ww.openweathermap.org",
                    "text" => $message,
                    "ts" => $date->format('U')
                ]
            ]
        ];
    }
}