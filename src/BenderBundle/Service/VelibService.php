<?php

namespace BenderBundle\Service;

use Symfony\Component\VarDumper\VarDumper;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;

/**
 * Class VelibService
 * @package BenderBundle\Service
 */
class VelibService extends BaseService
{
    public $titleMessage = "";
    /**
     * @var string
     */
    protected $hook = '!velib';
    private $api_url = "https://api.citybik.es/v2/networks/velib?fields=id,name,href,stations";

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

        switch ($commands[0]) {
            case "help":
                return $this->getHelp();
                break;
            default:
                $this->titleMessage = $commands[0];
                $message = [];
                $data = $this->get();
                foreach ($data->network->stations as $station) {
                    if (stristr($station->name, $commands[0])) {
                        $message[] = [
                            'title' => sprintf("à la station %s : %s vélo%s", $station->name, $station->free_bikes, ($station->free_bikes > 1 ? "s" : ""))
                        ];
                    }
                }
        }


        if (!empty($message)) {
            return $message;
        } else {
            return $this->array_random($this->badAnswer) . " je ne trouve pas cette station velib";
        }
    }

    /**
     * @return string
     */
    public function getHelp()
    {
        return "!velib (help|zone de velib)";
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    private function get()
    {
        $client = new Client();
        try {
            $result = $client->request("GET", $this->api_url);
            $json = $result->getBody()->getContents();
            $json = \GuzzleHttp\json_decode($json);
            return $json;
        } catch (ClientException $e) {
            return $this->array_random($this->badAnswer);
        }
    }

    protected function getAnswer($message)
    {

        $date = new \DateTime();
        return [
            "attachments" => [
                [
                    "title" => "Velib : " . $this->titleMessage,
                    "color" => "#224488",
                    "footer" => "Velib",
                    "footer_icon" => $this->getMediaUrl("/bundles/bender/icons/velib.png"),
                    "title_link" => "http://www.velib.paris/",
                    "fields" => !is_array($message) ? [$message] : $message,
                    "ts" => $date->format('U')
                ]
            ]
        ];
    }
}
