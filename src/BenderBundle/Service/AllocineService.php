<?php

namespace BenderBundle\Service;

/**
 * Class AllocineService
 * @package BenderBundle\Service
 */
class AllocineService extends BaseService
{
    protected $hook = '!allocine';

    /**
     * @inheritdoc
     */
    public function getHelp()
    {
        return '!allocine';
    }

    /**
     * @inheritdoc
     */
    public function getDatas()
    {
        $url = 'http://rss.allocine.fr/ac/cine/cettesemaine?fmt=xml';

        $xml = $this->get_page_content($url);

        $movies = array();
        $source = new \SimpleXMLElement($xml);

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

    /**
     * @inheritdoc
     */
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