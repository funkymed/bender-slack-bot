<?php

namespace BenderBundle\Service;

/**
 * Class RecetteService
 * @package BenderBundle\Service
 */
class RecetteService extends BaseService
{
    protected $hook = '!recette';

    public function getHelp()
    {
        return '!recette';
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
        $content_page_redirect = explode('</html>',$this->get_page_content("http://www.marmiton.org/recettes/recette-hasard.aspx"));

        $doc = new \DOMDocument();
        $doc->loadHTML($content_page_redirect[0].'</html>');

        $links = $doc->getElementsByTagName('a');
        $link_recipe = "";
        foreach ($links as $link) {
            $link_recipe = urldecode("http://www.marmiton.org".$link->getAttribute('href'));
        }

        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $content_page = file_get_contents($link_recipe,false,$context);

        $doc = new \DOMDocument();

        $previous_value = libxml_use_internal_errors(TRUE);
        $doc->loadHTML((string)$content_page);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_value);

        $links = $doc->getElementsByTagName('h1');

        $recette="";
        foreach ($links as $link) {
           $recette = $link->nodeValue;
        }
        
        $recette = trim(preg_replace('/\s\s+/', ' ', $recette));

        return $recette.":".$link_recipe;
    }

    protected function getAnswer($message){
        if(is_array($message))
            $message=implode("\n",$message);

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"marmiton.org",
                    "color"=> "#F47422",
                    "footer"=> "marmiton.org",
                    "footer_icon"=>$this->getContainer()->getParameter('url_bender')."/bundles/bender/icons/marmiton.png",
                    "title_link"=> "http://www.marmiton.org",
                    "text"=>$message,
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }

}
