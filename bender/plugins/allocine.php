<?php 
/**
 * Class plugin_allocine
 */

class AllocineMovie
{
    public $name;
    public $poster;
    public $url;
    public $numberOfTheatre;

    public function __construct($name, $poster, $url, $numberOfTheatre)
    {
        $this->name = $name;
        $this->poster = $poster;
        $this->url = $url;
        $this->numberOfTheatre = $numberOfTheatre;
    }

    static function sortByPopularity(AllocineMovie $a, AllocineMovie $b)
    {
        $diff = $a->numberOfTheatre - $b->numberOfTheatre;

        return ($diff < -1 ? +1 : ($diff > 1 ? -1 : $diff));
    }
}

class plugin_allocine extends Plugin
{
    protected $hook = '!allocine';

    public function getHelp()
    {
        return '!allocine';
    }

    public function getDatas()
    {
        $url = 'http://rss.allocine.fr/ac/cine/cettesemaine?fmt=xml';

        $xml = $this->get_page_content($url);

        $movies = array();
        $source = new SimpleXMLElement($xml);

        $source->addAttribute('encoding', 'UTF-8');

        foreach ($source->channel->item as $movie) {
            $title          = (string)$movie->title;
            $poster         = (string)$movie->enclosure->attributes()->url;
            $description    = (string)$movie->description;

            preg_match('#<a href="([^"]+)">Fiche complète du film</a> \|#', $description, $match);
            $url = $match[1];

            preg_match('#Séances des ([0-9]+) cinémas</a> \| #', $description, $match);
            $popularity = isset($match[1]) ? intval($match[1]) : 1;

            $movies[] = new AllocineMovie($title, $poster, $url, $popularity);
        }

        usort($movies, array('AllocineMovie', 'sortByPopularity'));

        return $movies;
    }

    public function getMessage($text)
    {
        $movies = $this->getDatas();
        $message = array('Les films de la semaine :');
        $tmpl = '<a href="#url#"><img src="#img#" /></a>';
        $replacer = array('#url#','#img#');
        for($r=0;$r<count($movies);$r++)
        {
            if($r<10)
            {
                //$message[]=(str_replace($replacer,array($movies[$r]->url,$movies[$r]->poster),$tmpl));
                $message[]=$movies[$r]->name.' '.$movies[$r]->url;
            }
        }
        return implode("\n",$message);

    }

}

return new plugin_allocine();