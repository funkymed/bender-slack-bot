<?php

namespace BenderBundle\Service;

use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\HttpFoundation\Session\Session;

class LolService extends BaseService
{
    /**
     * @var string
     */
    protected $hook = '!lol';

    private $list1;
    private $list2;
    private $list3;
    private $list4;
    private $list5;
    private $star;
    private $lieu;
    private $tv;
    private $adj;
    private $phrase;

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!lol optional (lol|random|citation|jcvd)';
    }

    /**
     * @return array
     */
    public function makeCitation() {
        $citation = $this->getContainer()->getParameter("bender.citation");
        return ">".$this->array_random($citation);
    }

    /**
     * @return array
     */
    public function makeJcvd() {
        $jcvd = $this->getContainer()->getParameter("bender.jcvd");
        return "> *Jean-Claude Van Damme* : ".$this->array_random($jcvd);
    }

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
        $command = isset($commands[0]) ? $commands[0] : false;
        switch($command)
        {
            case "lol":
                return $this->makeLol();
                break;
            case "random":
                return $this->makeRandom();
                break;
            case "jcvd":
                return $this->makeJcvd();
                break;
            case "citation":
                return $this->makeCitation();
                break;
            case "help":
                return $this->getHelp();
                break;
            default:
                $rand = rand(1,4);
                if($rand==1)
                {
                    return $this->makeLol();
                }elseif($rand==2){
                    return $this->makeRandom();
                }elseif($rand==3){
                    return $this->makeJcvd();
                }else{
                    return $this->makeCitation();
                }
                break;
        }
    }

    /**
     * @return string
     */
    private function makeRandom()
    {
        $this->list1    = $this->getContainer()->getParameter("bender.01");
        $this->list2    = $this->getContainer()->getParameter("bender.02");
        $this->list3    = $this->getContainer()->getParameter("bender.03");
        $this->list4    = $this->getContainer()->getParameter("bender.04");
        $this->list5    = $this->getContainer()->getParameter("bender.05");
        $phrase = array(
          UCFirst($this->array_random($this->list1)).",",
          $this->array_random($this->list2),
          $this->array_random($this->list3),
          $this->array_random($this->list4),
          $this->array_random($this->list5),
        );

        return implode(' ',$phrase);
    }

    /**
     * @return array|string
     */
    public function makeLol()
    {
        $this->star     = $this->getContainer()->getParameter("bender.star");
        $this->lieu     = $this->getContainer()->getParameter("bender.lieu");
        $this->tv       = $this->getContainer()->getParameter("bender.tv");
        $this->adj      = $this->getContainer()->getParameter("bender.adj");
        $this->phrase   = $this->getContainer()->getParameter("bender.phrase");

        $rand   = rand(1,10);
        $numtxt = rand(1,4);
        $star   = $this->array_random($this->star);
        $lieu   = $this->array_random($this->lieu);
        $tv     = $this->array_random($this->tv);
        $adj    = $this->array_random($this->adj);
        $phrase = $this->array_random($this->phrase);

        switch($rand)
        {
            case 1:
                $message="ça branche quelqu'un ".$numtxt." places pour ".$star." ".$lieu." ?";
                break;
            case 2:
                $message="je suis abonné à la chaine de ".$star;
                break;
            case 3:
                $message="hier je suis allé ".$lieu." et j'ai pas mal marché";
                break;
            case 4:
                $message="quelqu'un connait un bon site sur ".$star." ?";
                break;
            case 5:
                $message="j'ai participé à l'enregistrement de ".$tv." et ".$star." était ".$adj;
                break;
            default:
            case 6:
                $message=$phrase;
                break;
            case 7:
                $message="wow cool, y a ".$star." sur ".$tv." à ".$numtxt."H du mat";
                break;
            case 8:
                $message="vous connaissez ".$star." ? ";
                break;
            case 9:
                $message="j'ai un faux air de ".$star." vous ne trouvez pas ?";
                break;
            case 10:
                $message="j'habite juste a côté, ".$lieu." je croise parfois ".$star;
                break;

        }


        $message = str_replace("de les ","des",$message);

        return UCFirst($message);
    }
}
