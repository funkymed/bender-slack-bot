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
    public $titleMessage = "";
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

                $message=[];
                $now = new \DateTime();
                $this->titleMessage = sprintf("Au départ de %s à destination de %s le %s",strtoupper($action[0]),strtoupper($action[1]), $now->format('d/m/Y'));
                $journey =$this->getJourney($fromID,$toID,$now);
                if($journey){
                    $message[]= [
                        'title'=>sprintf("départ à %s arrivé à %s",$journey['departure']->format('H\hi'),$journey['arrival']->format('H\hi'))
                    ];
                    $journeys=[];
                    for($r=1;$r<=2;$r++){
                        $now = $journey['departure'];
                        $interval = new \DateInterval('PT1M');
                        $now->add($interval);
                        $journey =$this->getJourney($fromID,$toID,$now);
                        if($journey) {
                            $journeys[] = $journey;
                            $message[]= [
                                'title'=>sprintf("départ à %s arrivé à %s",$journey['departure']->format('H\hi'),$journey['arrival']->format('H\hi'))
                            ];
                        }
                    }
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

    private function getJourney($fromID,$toID,$now){
        $url = sprintf("/journeys?from=%s&to=%s&datetime=%sT%s",$fromID,$toID,$now->format('Ymd'),$now->format('His'));
        $result = $this->send($url);
        if(isset($result->journeys)) {
            return [
                "departure"=>new \DateTime($result->journeys[0]->departure_date_time),
                "arrival"=>new \DateTime($result->journeys[0]->arrival_date_time),

            ];
        }else{
            return false;
        }
    }

    protected function getAnswer($message){

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"SNCF : ".$this->titleMessage,
                    "color"=> "#E41F26",
                    "footer"=> "Sncf",
                    "footer_icon"=>$this->getMediaUrl("/bundles/bender/icons/sncf.png"),
                    "title_link"=> "http://www.sncf.com",
                    "fields"=>!is_array($message) ? [$message] : $message,
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }
}

