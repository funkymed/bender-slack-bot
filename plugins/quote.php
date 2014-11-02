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
        return '!quote (help|add username text)';
    }

    /**
     * @return mixed
     */
    public function getQuotes()
    {
        $quotes = file_get_contents(__DIR__.'/data/quotes.txt');
        if(!$quotes){

            @file_put_contents(__DIR__.'/data/quotes.txt', json_encode(array()), FILE_APPEND);
            return $this->getQuotes();
        }
        return json_decode($quotes);
    }

    /**
     * @param $user_name
     * @param $quote
     */
    public function addQuotes($user_name,$quote)
    {
        global $user_name;
        $quotes  = $this->getQuotes();
        $message = "Ajouté par ".$user_name." le ".date('d/m/Y')."\n";
        $message.= $user_name." : ".$quote;
        $quotes[]=$message;
        $res = @file_put_contents(__DIR__.'/data/quotes.txt', json_encode($quotes));
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
                return $this->array_random($quotes);
            else
                return "Pas encore de quote";
        }

        switch($commands[0])
        {
            case "add":
                $user = $commands[1];
                unset($commands[0]);
                unset($commands[1]);
                $res = $this->addQuotes($user,implode(' ', $commands));
                return $res ? "Quote ajoutée" : "Impossible d'ajouter cette quote";
            case "help":
                return $this->getHelp();
                break;
            default:
                return $this->array_random($this->badAnswer)." J'ai rien capté !";
                break;
        }

    }
}

return new plugin_quote();

