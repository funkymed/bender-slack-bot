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

        usort($movies, array('\BenderBundle\Service\AllocineMovie', 'sortByPopularity'));

        return $movies;
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
        $movies = $this->getDatas();
        $message = [];

        for($r=0;$r<count($movies);$r++)
        {
            if($r<10)
            {
                $message[]= $movies[$r]->name." : ".$movies[$r]->url;
            }
        }
        return $message;

    }

    protected function getAnswer($message){
        if(is_array($message))
            $message=implode("\n",$message);

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"Allociné - Les films de la semaine",
                    "color"=> "#FEC80F",
                    "footer"=> "Allociné",
                    "footer_icon"=>$this->getMediaUrl("/bundles/bender/icons/allocine.png"),
                    "title_link"=> "http://ww.allocine.com",
                    "text"=>$message,
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }
}
