<?php

/**
 * Class plugin_movie
 */
class plugin_movie extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!movie';

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
        return '!movie search nom du film';
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text) {

        $commands = $this->getCommands($text);
        if(!isset($commands[0]))
            return $this->array_random($this->badAnswer)." Il manque une commande (search, new, trailer)";

        switch($commands[0])
        {
            case "search":
                if(!isset($commands[1]))
                    return $this->array_random($this->badAnswer);

                $search = $commands;
                unset($search[0]);
                $search = implode(' ',$search);

                $omdbapi = @file_get_contents('http://www.omdbapi.com/?r=json&s='.urlencode($search));
                if($omdbapi)
                {
                    $res = array();
                    $result = json_decode($omdbapi);

                    if(isset($result->Search))
                    {
                        foreach($result->Search as $result_movie)
                        {
                            $title  = $result_movie->Title;
                            $year   = $result_movie->Year;
                            $imdbID = $result_movie->imdbID;

                            $res[]= "$title ($year) http://www.imdb.com/title/$imdbID/";
                        }
                        return implode("\n",$res);

                    }else{
                        return "Désolé, j'ai rien trouvé...";
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

return new plugin_movie();

