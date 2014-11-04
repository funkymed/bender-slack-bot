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
        $this->star     = include "data/star.php";
        $this->lieu     = include "data/lieu.php";
        $this->tv       = include "data/tv.php";
        $this->adj      = include "data/adj.php";
        $this->phrase   = include "data/phrase.php";
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text) {
        $rand = rand(1,2);
        if($rand==2)
        {
            return $this->makeRandom();
        }else{
            return $this->makeLol();
        }
    }

    /**
     * @return string
     */
    private function makeRandom()
    {
        $phrase = array(
          $this->array_random($this->list1),
          $this->array_random($this->list2),
          $this->array_random($this->list3),
          $this->array_random($this->list4)
        );

        return implode(' ',$phrase);
    }

    /**
     * @return array|string
     */
    public function makeLol() {

        $rand = rand(1,10);
        $numtxt = rand(1,9);

        $star = $this->array_random($this->star);
        $lieu = $this->array_random($this->lieu);
        $tv = $this->array_random($this->tv);
        $adj = $this->array_random($this->adj);
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
                $message="j'ai participé a l'enregistrement de ".$tv." et ".$star." est ".$adj;
                break;
            default:
            case 6:
                $message=$phrase;
                break;
            case 7:
                $message="wow cool, y a ".$star." dans ".$tv." à ".$numtxt."H";
                break;
            case 8:
                $message="vous connaissez ".$star." ? ";
                break;
            case 9:
                $message="j'ai un faux air de ".$star;
                break;
            case 10:
                $message="j'habite juste a côté, ".$lieu;
                break;
        }

        return $message;
    }
}

return new plugin_lol();

