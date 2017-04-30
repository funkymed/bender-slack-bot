<?php

namespace BenderBundle\Service;

/**
 * Class GifService
 * @package BenderBundle\Service
 */
class GifService extends BaseService
{
    protected $hook = '!gif';
    private $query;

    public function getHelp()
    {
        return '!gif query';
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
        $query = urlencode(implode(' ', $commands));
        $this->query = $query;
        $apikey = $this->getContainer()->getParameter('giphy_key');
        $res = $this->get_page_content('http://api.giphy.com/v1/gifs/search?q=' . $query . '&api_key=' . $apikey);
        if ($res) {
            $json = json_decode($res);
            shuffle($json->data);
            return $json->data[0]->images->fixed_height->url;
        } else {
            return null;
        }

    }

    protected function getAnswer($message)
    {
        if (is_array($message))
            $message = implode("\n", $message);

        $date = new \DateTime();
        return [
            "attachments" => [
                [
                    "title" => $this->query,
                    "color" => $this->color,
                    "footer" => "Giphy",
                    "footer_icon" => $this->getMediaUrl("/bundles/bender/icons/gify.png"),
                    "te" => "http://www.giphy.com",
                    "image_url" => $message,
                    "ts" => $date->format('U')
                ]
            ]
        ];
    }
}
