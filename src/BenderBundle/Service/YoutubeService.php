<?php

namespace BenderBundle\Service;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class YoutubeService
 * @package BenderBundle\Service
 */
class YoutubeService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!youtube';

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!youtube search nom de la vidéo';
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text) {

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

                $url = sprintf("https://www.googleapis.com/youtube/v3/search?part=snippet&q=%s&key=%s&orderby=relevance&max-results=1&strict=true",urlencode($search),$this->getContainer()->getParameter('api_youtube_key'));
                $api = $this->get_page_content($url);
                $api = json_decode($api);
                if(!empty($api->items)){

                    foreach($api->items as $item){
                        if(isset($item->id->videoId)){
                            return sprintf("https://www.youtube.com/watch?v=%s",$item->id->videoId);
                        }
                    }

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
}


