<?php

namespace BenderBundle\Service;

class QuoteService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!quote';

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
        if (!isset($commands[0])) {
            $quotes = $this->getQuotes();
            if (count($quotes) > 0)
                return stripslashes($this->array_random($quotes));
            else
                return "Pas encore de quote";
        }

        switch ($commands[0]) {
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
                return $quotes[count($quotes) - 1];
                break;
            default:
                return $this->array_random($this->badAnswer) . " J'ai rien capté !";
                break;
        }

    }

    /**
     * @param $user
     * @param $quote
     * @return bool
     */
    public function addQuotes($user, $quote)
    {
        $user_name = $this->getUserName();
        $team_domain = $this->getTeamDomain();

        $message = "Ajouté par " . $user_name . " le " . date('d/m/Y') . "\n";
        $message .= $user . " : " . stripslashes($quote);

        $quotes = $this->getQuotes();
        $quotes[] = $message;

        $res = $this->cache->save("quote_" . $team_domain, $quotes);

        return $res ? true : false;
    }

    /**
     * @return false|mixed
     */
    public function getQuotes()
    {
        $quotes = $this->cache->fetch($this->getKeyCache());
        return $quotes ? $quotes : [];
    }

    public function getKeyCache()
    {
        $team_domain = $this->getTeamDomain();
        return 'quote_' . $team_domain;
    }

    public function getHelp()
    {
        return '!quote (last|all|help|add username text)';
    }
}
