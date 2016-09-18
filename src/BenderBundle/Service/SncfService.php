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
    private $message = "";
    private $api_url = "https://api.sncf.com/v1/coverage/sncf";
    /**
     * @var string
     */
    protected $hook = '!sncf';

    /**
     * @return string
     */
    public function getHelp()
    {
        return "!sncf (next|help) gare de départ,gare d'arrivé";
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text) {
        $answer = $this->checkAnswer($text);
        return $answer ? $this->getAnswer($answer) : "";
    }

    /**
     * @param $text
     * @return array|string
     */
    public function checkAnswer($text) {

        $commands = $this->getCommands($text);

        switch($commands[0]){
            case "help":
                return $this->getHelp();
                break;
            case "next":
                unset($commands[0]);
                $action = explode(",",implode(" ",$commands));

                if(empty($action[0])){
                    $message = $this->array_random($this->badAnswer)." Il faut me donner une gare de départ";
                    break;
                }

                if(empty($action[1])){
                    return $this->array_random($this->badAnswer)." Il faut me donner une gare de d'arrivé";
                    break;
                }

                $fromID = $this->getStationID($action[0]);
                $toID = $this->getStationID($action[1]);
                $url = sprintf("/journeys?from=%s&to=%s",$fromID,$toID);
                $result = $this->send($url);
                if(isset($result->journeys)) {
                    $departure = new \DateTime($result->journeys[0]->departure_date_time);
                    $arrival = new \DateTime($result->journeys[0]->arrival_date_time);
                    $message = sprintf("Au départ de %s à %s pour arriver à %s le %s", $action[0], $departure->format('d/m/Y à H\hi'), $action[1], $arrival->format('d/m/Y à H\hi'));
                }
                break;
            default:
                $message = "Je conseil de taper `!sncf help`, je pense que tu en as bien besoin.";
        }


        if(!empty($message)){
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
            return $this->array_random($this->badAnswer);
        }
    }

    protected function getAnswer($message){
        if(is_array($message))
            $message=implode("\n",$message);

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"SNCF",
                    "color"=> $this->color,
                    "footer"=> "Sncf",
                    "footer_icon"=>$this->getContainer()->getParameter('url_bender')."/images/icons/sncf.png",
                    "title_link"=> "http://www.sncf.com",
                    "text"=>$message,
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }
}

