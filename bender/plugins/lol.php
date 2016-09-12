<?php

/**
 * Class plugin_lol
 */
class plugin_lol extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!lol';

    public function __construct()
    {
        $this->list1    = include "data/01.php";
        $this->list2    = include "data/02.php";
        $this->list3    = include "data/03.php";
        $this->list4    = include "data/04.php";
        $this->list5    = include "data/05.php";
        $this->star     = include "data/star.php";
        $this->lieu     = include "data/lieu.php";
        $this->tv       = include "data/tv.php";
        $this->adj      = include "data/adj.php";
        $this->phrase   = include "data/phrase.php";
    }

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
        $citation = include "data/citation.php";
        return $this->array_random($citation);
    }

    /**
     * @return array
     */
    public function makeJcvd() {
        $jcvd = include "data/jcvd.php";
        return $this->array_random($jcvd);
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text)
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
    public function makeLol() {

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
                $message="je suis hyper fan de ".$star;
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

return new plugin_lol();

