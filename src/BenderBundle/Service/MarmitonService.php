<?php

namespace BenderBundle\Service;

use Symfony\Component\VarDumper\VarDumper;
use sylouuu\MarmitonCrawler\Recipe\Recipe;

/**
 * Class RecetteService
 * @package BenderBundle\Service
 */
class MarmitonService extends BaseService
{
    protected $hook = '!marmiton';

    public function getHelp()
    {
        return '!marmiton';
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
        $content_page_redirect = explode('</html>', $this->get_page_content("http://www.marmiton.org/recettes/recette-hasard.aspx"));

        $doc = new \DOMDocument();
        $doc->loadHTML($content_page_redirect[0] . '</html>');

        $links = $doc->getElementsByTagName('a');
        $link_recipe = "";
        foreach ($links as $link) {
            $link_recipe = urldecode("http://www.marmiton.org" . $link->getAttribute('href'));
        }

        // Fetch the recipe
        $recipe = new Recipe($link_recipe);
        $recipe = json_decode($recipe->getRecipe());
        $message = sprintf("%s (pour %s))\n", $recipe->recipe_name, $recipe->guests_number);
        $message .= sprintf("Préparation : %s\n", $recipe->preparation_time);
        $message .= sprintf("Cuisson : %s\n", $recipe->cook_time);
        $message .= ">instructions:\n";
        $message .= sprintf("%s\n", $recipe->instructions);
        $message .= ">ingredients:\n";
        foreach ($recipe->ingredients as $ingredient) {
            $message .= "\t- " . $ingredient . "\n";
        }
        return ["text" => $message, "url" => $link_recipe];
        exit;


        $opts = array('http' => array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $content_page = file_get_contents($link_recipe, false, $context);

        $doc = new \DOMDocument();

        $previous_value = libxml_use_internal_errors(TRUE);
        $doc->loadHTML((string)$content_page);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_value);

        $links = $doc->getElementsByTagName('h1');

        $recette = "";
        foreach ($links as $link) {
            $recette = $link->nodeValue;

        }

        $img_recipe = "";
        $images = $doc->getElementsByTagName('img');
        foreach ($images as $img) {
            if ($img->getAttribute('class') == "photo m_pinitimage") {
                $img_recipe = $img->getAttribute('src');
            }
        }
        $recette = trim(preg_replace('/\s\s+/', ' ', $recette));

        return ["text" => $recette . " : " . $link_recipe, "image" => $img_recipe];
    }

    protected function getAnswer($message)
    {
        $date = new \DateTime();
        $data = [
            "attachments" => [
                [
                    "title" => "Recette Marmiton",
                    "color" => "#F47422",
                    "footer" => "marmiton.org",
                    "footer_icon" => $this->getMediaUrl("/bundles/bender/icons/marmiton.png"),
                    "title_link" => $message['url'],
//                    "image_url"=>$message['image'],
                    "text" => $message['text'],
                    "ts" => $date->format('U')
                ]
            ]
        ];
        if (!empty($message['image'])) {
            $data['attachments'][0]['image_url'] = $message['image'];
        }

        return $data;
    }

}
