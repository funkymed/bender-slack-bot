<?php

namespace BenderBundle\Service;

/**
 * Class GifService
 * @package BenderBundle\Service
 */
class GifService extends BaseService
{
    protected $hook = '!gif';

    public function getHelp()
    {
        return '!gif';
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
        $commands   = $this->getCommands($text);
        $query      = urlencode(implode(' ',$commands));
        $res        = $this->get_page_content('http://api.giphy.com/v1/gifs/search?q='.$query.'&api_key=dc6zaTOxFJmzC');
        if($res)
        {
            $json = json_decode($res);
            shuffle($json->data);
            return $json->data[0]->images->fixed_height_downsampled->url;
        }else{
            return null;
        }

    }

    protected function getAnswer($message){
        if(is_array($message))
            $message=implode("\n",$message);
        return [
            "attachments"=>[
                [
                    "title"=>"Giphy",
                    "text"=>$message
                ]
            ]
        ];
    }
}
