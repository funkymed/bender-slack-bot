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
        $list1  = include "data/01.php";
        $list2  = include "data/02.php";
        $list3  = include "data/03.php";
        $list4  = include "data/04.php";
        $phrase = array(
          $this->array_random($list1),
          $this->array_random($list2),
          $this->array_random($list3),
          $this->array_random($list4)
        );

        return implode(' ',$phrase);
    }

    /**
     * @return array|string
     */
    public function makeLol() {

        $rand = rand(1,10);
        $numtxt = rand(1,9);

        $star = include "data/star.php";
        $star = $this->array_random($star);

        $lieu = include "data/lieu.php";
        $lieu = $this->array_random($lieu);

        $tv = include "data/tv.php";
        $tv = $this->array_random($tv);

        $adj = include "data/adj.php";
        $adj = $this->array_random($adj);

        $phrase = include "data/phrase.php";
        $phrase = $this->array_random($phrase);

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

