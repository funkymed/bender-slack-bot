<?php

namespace BenderBundle\Service;

/**
 * Class WikipediaService
 * @package BenderBundle\Service
 */
class WikipediaService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!wikipedia';

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!wikipedia search query_word';
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
        if(!isset($commands[0]))
            return $this->array_random($this->badAnswer)." Il manque une commande (search, help)";

        switch($commands[0])
        {
            case "search":
                if(!isset($commands[1]))
                    return $this->array_random($this->badAnswer);

                $search = $commands;
                unset($search[0]);
                $search = implode(' ',$search);

                $api = @file_get_contents("http://fr.wikipedia.org/w/api.php?action=query&list=search&format=json&&rawcontinue&srsearch=".urlencode($search));

                if($api)
                {
                    $api = json_decode($api);
                    $response = array();
                    $response[]=$api->query->search[0]->title." "."http://fr.wikipedia.org/wiki/" . str_replace(" ","_",$api->query->search[0]->title);
                    $response[]=strip_tags($api->query->search[0]->snippet);
                    $others = array();
                    foreach($api->query->search as $k=>$q)
                    {
                        if($k>0)
                            $others[]=$q->title;
                    }
                    if(count($others)>0)
                        $response[]="Autres recherche :\n".implode(", ", $others);
                    return implode("\n",$response);
                }else{
                    return $this->array_random($this->badAnswer);
                }
                break;

            case "help":
                return $this->getHelp();
                break;
            default:
                return $this->array_random($this->badAnswer)." J'ai rien capté !";
                break;
        }
    }

    protected function getAnswer($message){
        if(is_array($message))
            $message=implode("\n",$message);

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"Wikipedia",
                    "color"=> $this->color,
                    "footer"=> "Sncf",
                    "footer_icon"=>"http://findicons.com/files/icons/111/popular_sites/128/wikipedia_globe_icon.png",
                    "title_link"=> "http://fr.wikipedia.com",
                    "text"=>$message,
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }
}

