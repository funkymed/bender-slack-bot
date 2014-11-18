<?php

/**
 * Class plugin_quote
 */
class plugin_quote extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!quote';

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

    public function getHelp()
    {
        return '!quote (last|all|help|add username text)';
    }

    /**
     * @return mixed
     */
    public function getQuotes()
    {
        global $user_name,$team_domain;
        $path = dirname(__FILE__);
        $filename = $path.'/data/'.$team_domain.'_quotes.txt';

        if (file_exists($filename)) {

            $quotes = @file_get_contents($filename);
            return $quotes && $quotes!='' ? unserialize($quotes) : array();
        }else{
            @file_put_contents($filename, serialize(array()));
            return array();
        }
    }

    /**
     * @param $user_name
     * @param $quote
     */
    public function addQuotes($user,$quote)
    {
        global $user_name,$team_domain;

        $message = "Ajouté par ".$user_name." le ".date('d/m/Y')."\n";
        $message.= $user." : ".stripslashes($quote);

        $quotes  = $this->getQuotes();
        $quotes[]=$message;

        $path = dirname(__FILE__);
        $res  = @file_put_contents($path.'/data/'.$team_domain.'_quotes.txt', serialize($quotes));

        return $res ? true : false;
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text) {

        $commands = $this->getCommands($text);
        if(!isset($commands[0]))
        {
            $quotes = $this->getQuotes();
            if(count($quotes)>0)
                return stripslashes($this->array_random($quotes));
            else
                return "Pas encore de quote";
        }

        switch($commands[0])
        {
            case "add":
                $user = $commands[1];
                unset($commands[0]);
                unset($commands[1]);
                $res = $this->addQuotes($user, implode(' ', $commands));
                return $res ? "Quote ajoutée" : "Impossible d'ajouter cette quote";
            case "help":
                return $this->getHelp();
                break;
            case "all":
                $quotes = $this->getQuotes();
                return $quotes;
                break;
            case "last":
                $quotes = $this->getQuotes();
                return $quotes[count($quotes)-1];
                break;

            default:
                return $this->array_random($this->badAnswer)." J'ai rien capté !";
                break;
        }

    }
}

return new plugin_quote();

