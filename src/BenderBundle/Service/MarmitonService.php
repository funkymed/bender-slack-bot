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
        //Random link
        $content_page_redirect = explode('</html>', $this->get_page_content("http://www.marmiton.org/recettes/recette-hasard.aspx"));
        $doc = new \DOMDocument();
        $doc->loadHTML($content_page_redirect[0] . '</html>');
        $links = $doc->getElementsByTagName('a');
        $link_recipe = "";
        foreach ($links as $link) {
            $link_recipe = urldecode("http://www.marmiton.org" . $link->getAttribute('href'));
        }

        //Parse link data
        $recipe = new Recipe($link_recipe);
        $recipe = json_decode($recipe->getRecipe());
        $message = sprintf("Pour %s personnes\n", $recipe->guests_number);
        $message .= sprintf("Préparation : %s minutes\n", $recipe->preparation_time);
        $message .= sprintf("Cuisson : %s minutes\n", $recipe->cook_time);
        $message .= "Instructions :\n";
        $message .= sprintf("%s\n", $recipe->instructions);
        $message .= "ingredients : \n";
        foreach ($recipe->ingredients as $ingredient) {
            $message .= "\t- " . $ingredient . "\n";
        }
        return ["title" => $recipe->recipe_name, "text" => $message, "url" => $link_recipe];

    }

    protected function getAnswer($message)
    {
        $date = new \DateTime();
        $data = [
            "attachments" => [
                [
                    "title" => $message['title'],
                    "color" => "#F47422",
                    "footer" => "marmiton.org",
                    "footer_icon" => $this->getMediaUrl("/bundles/bender/icons/marmiton.png"),
                    "title_link" => $message['url'],
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
