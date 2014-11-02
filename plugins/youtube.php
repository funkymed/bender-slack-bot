<?php

/**
 * Class plugin_youtube
 */
class plugin_youtube extends Plugin
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
        $obj = new stdClass();
        $obj->title = $title;
        $obj->link = $yt->link[0]->href;
        return $obj;
    }

    /**
     * @param $feed
     * @return string
     */
    private function feedVideo($feed)
    {
        $result = json_decode($feed);

        if(isset($result->feed->entry))
        {
            foreach($result->feed->entry as $yt)
            {
                $video = $this->getVideoDetail($yt);
                $res[]= $video->title." ".$video->link;
            }
            return implode("\n",$res);

        }else{
            return "Désolé, j'ai rien trouvé...";
        }
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

                $api = @file_get_contents("https://gdata.youtube.com/feeds/api/videos?q=".urlencode($search)."&orderby=relevance&max-results=1&strict=true&v=2&alt=json&hl=fr");

                if($api)
                {
                    return $this->feedVideo($api);
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

return new plugin_youtube();

