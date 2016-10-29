<?php

namespace BenderBundle\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Intervention\Image\ImageManagerStatic;

/**
 * Class YoutubeService
 * @package BenderBundle\Service
 */
class MemeService extends BaseService
{

    //Doc : http://version1.api.memegenerator.net/#Generators_Search
    private $url_search = "http://version1.api.memegenerator.net/Generators_Search?q=%s&pageIndex=0&pageSize=5";
    /**
     * @var string
     */
    protected $hook = '!meme';

    /**
     * @return string
     */
    public function getHelp()
    {
        return '!meme help|create mood|url;title;message';
    }

    /**
     * @param $text
     * @return array|string
     */
    public function getMessage($text) {
        $answer = $this->checkAnswer($text);
        return $answer ? $this->getAnswer($answer) : "";
    }

    /**
     * @param $text
     * @return array|string
     */
    public function checkAnswer($text) {

        $commands = $this->getCommands($text);
        if(!isset($commands[0]))
            return $this->array_random($this->badAnswer)." Il manque une commande (search, help)";

        switch($commands[0])
        {
            case "create":
                if(!isset($commands[1]))
                    return $this->array_random($this->badAnswer);

                $search = $commands;
                unset($search[0]);
                $search = implode(' ',$search);
                $query = explode(';',$search);
                if(count($query)!=3){
                    return $this->array_random($this->badAnswer);
                }


                $image_source = false;
                if(strstr($query[0],'://'))
                {
                    $image_source = $query[0];
                }else{
                    $imageRes = $this->getMemeImage($query[0]);

                    if($imageRes->success) {
                        $image_source = $this->array_random($imageRes->result)->imageUrl;
                    }
                }

                if($image_source) {
                    $path = realpath($this->getContainer()->get('kernel')->getRootDir()."/../web/meme/");
                    $filename = $this->getRandomFilename().".png";
                    $img = ImageManagerStatic::make($image_source);
                    $img->text($query[1], $img->width()/2, 10, function($font) {
                        $fontPath = $this->getContainer()->get('kernel')->locateResource('@BenderBundle/Resources/assets/font/impact.ttf');
                        $font->file($fontPath);
                        $font->size(32);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('top');
                    })
                    ->text($query[2], $img->width()/2, $img->height()-30, function($font) {
                        $fontPath = $this->getContainer()->get('kernel')->locateResource('@BenderBundle/Resources/assets/font/impact.ttf');
                        $font->file($fontPath);
                        $font->size(32);
                        $font->color('#ffffff');
                        $font->align('center');
                        $font->valign('bottom');
                    })
                    ->save($path."/".$filename);
                    $request = $this->getContainer()->get('request_stack')->getCurrentRequest();
                    $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                    $message = $host."/meme/".$filename;
                    return $message;
                }

                break;

            case "help":
                return $this->getHelp();
                break;
            default:
                return $this->array_random($this->badAnswer);
                break;
        }
    }

    private function getRandomFilename($length = 12)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @return mixed|\Psr\Http\Message\ResponseInterface|string
     */
    private function getMemeImage($query)
    {
        $client = new Client();
        try {
            $result = $client->request("GET", sprintf($this->url_search,$query));
            $json = $result->getBody()->getContents();
            $json = \GuzzleHttp\json_decode($json);
            return $json;
        } catch (ClientException $e) {
            return $this->array_random($this->badAnswer);
        }
    }

    protected function getAnswer($message){
        if(is_array($message))
            $message=implode("\n",$message);

        $date = new \DateTime();
        return [
            "attachments"=>[
                [
                    "title"=>"Meme generator",
                    "color"=> "#ff5555",
                    "footer"=> "Meme",
                    "image_url"=>$message,
                    "ts"=> $date->format('U')
                ]
            ]
        ];
    }

}


