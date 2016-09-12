<?php

/**
 * Class plugin_wikipedia
 */
class plugin_wikipedia extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!wikipedia';

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
        return '!wikipedia search query_word';
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
}

return new plugin_wikipedia();

