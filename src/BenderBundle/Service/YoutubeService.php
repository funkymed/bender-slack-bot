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
     * @var array
     */
    private $badAnswer = array(
        'Tu recherche quoi ?',
        "T'es relou là !",
        "Il manque un truc...",
        "Tu sais pas taper ?",
        "Patate !",
    );

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!youtube search nom de la vidéo';
    }

    /**
     * @param $yt
     * @return stdClass
     */
    private function getVideoDetail($yt)
    {
        $title = '';
        foreach($yt->title as $t)
        {
            $title = $t;
        }
        $obj = new \stdClass();
        $obj->title = $title;
        $obj->link = $yt->link[0]->href;

        VarDumper::dump($obj);exit;
        return $obj;
    }

    /**
     * @param $feed
     * @return string
     */
    private function feedVideo($result)
    {
//        VarDumper::dump($result->id->videoId);exit;
        return sprintf("https://www.youtube.com/watch?v=%s",$result->id->videoId);
//                $video = $this->getVideoDetail($yt);
//                $res[]= $video->title." ".$video->link;
//        }else{
//            return "Désolé, j'ai rien trouvé...";
//        }
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

//                $api = $this->get_page_content("https://gdata.youtube.com/feeds/api/videos?q=".urlencode($search)."&orderby=relevance&max-results=1&strict=true&v=2&alt=json&hl=fr");
                $url = sprintf("https://www.googleapis.com/youtube/v3/search?part=snippet&q=%s&key=%s&orderby=relevance&max-results=1&strict=true",urlencode($search),$this->getContainer()->getParameter('api_youtube_key'));
                $api = $this->get_page_content($url);
                $api = json_decode($api);
                if(!isset($api->error))
                {
//                    return $this->feedVideo($api->items[0]);
                    return sprintf("https://www.youtube.com/watch?v=%s",$api->items[0]->id->videoId);
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


