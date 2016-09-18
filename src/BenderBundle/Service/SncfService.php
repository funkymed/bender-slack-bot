<?php

namespace BenderBundle\Service;
use Symfony\Component\VarDumper\VarDumper;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

/**
 * Class RatpService
 * @package BenderBundle\Service
 */
class SncfService extends BaseService
{
    private $api_url = "https://api.sncf.com/v1/coverage/sncf";
    /**
     * @var string
     */
    protected $hook = '!sncf';

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
        return "!sncf gare de départ,gare d'arrivé";
    }

    /**
     * @param $text
     * @return string
     */
    public function getMessage($text) {

        $commands = $this->getCommands($text);

        if($commands[0]=='help')
            return $this->getHelp();

        $action = explode(",",implode(" ",$commands));
        if(!isset($action[0]))
            return $this->array_random($this->badAnswer)." Il faut me donner une gare de départ";

        if(!isset($action[1]))
            return $this->array_random($this->badAnswer)." Il faut me donner une gare de d'arrivé";

        $fromID = $this->getStationID($action[0]);
        $toID = $this->getStationID($action[1]);
        $url = sprintf("/journeys?from=%s&to=%s",$fromID,$toID);
        $result = $this->send($url);
        if(isset($result->journeys)){
            $departure = new \DateTime($result->journeys[0]->departure_date_time);
            $arrival = new \DateTime($result->journeys[0]->arrival_date_time);

            $message = sprintf("Au départ de %s à %s pour arriver à %s à %s",$action[0],$departure->format('d/m/Y à H:i'),$action[1],$arrival->format('d/m/Y à H:i'));

            return $message;
        }else{
            return $this->array_random($this->badAnswer)." Trajet impossible";
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    private function getStationID($name)
    {
        $url = sprintf("/places?q=%s",urlencode($name));
        $result = $this->send($url);
        return $result->places[0]->id;
    }

    /**
     * @param $url
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    private function send($url)
    {
        $apiKey = $this->getContainer()->getParameter('api_sncf_key');

        $defaultsParameters = [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $apiKey,
            ],
        ];

        $client = new Client();
        try {
            $result = $client->request("GET", $this->api_url.$url, $defaultsParameters);
            $json = $result->getBody()->getContents();
            $json = \GuzzleHttp\json_decode($json);
            return $json;
        } catch (ClientException $e) {
            return $this->array_random();
        }
    }
}