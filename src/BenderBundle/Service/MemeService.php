<?php

namespace BenderBundle\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use \GDText\Box;
use \GDText\Color;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Class YoutubeService
 * @package BenderBundle\Service
 */
class MemeService extends BaseService
{

    //Doc : http://version1.api.memegenerator.net/#Generators_Search
    private $url_search = "http://version1.api.memegenerator.net/Generators_Search?q=%s&pageIndex=0&pageSize=20";
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

                $path = realpath($this->getContainer()->get('kernel')->getRootDir()."/../web/meme/");
//                $filename = $this->getRandomFilename().".png";
                $filename = md5($search).".png";
                $request = $this->getContainer()->get('request_stack')->getCurrentRequest();
                $host = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

                if(file_exists($path."/".$filename)) {
                    return $host."/meme/".$filename;
                }

                if(count($query)<2){
                    return $this->array_random($this->badAnswer);
                }

                $image_source = false;
                if(strstr($query[0],'://'))
                {
                    $image_source = $query[0];
                }else{
                    $imageRes = $this->getMemeImage($query[0]);

                    if($imageRes->success && is_array($imageRes->result) && count($imageRes->result)>0) {
                        $image_source = $this->array_random($imageRes->result)->imageUrl;
                    }else{
                        return "nope, j'ai pas ça en stock";
                    }
                }

                if($image_source) {


                    $image = new ImageProcess($image_source);
                    $fontPath = $this->getContainer()->get('kernel')->locateResource('@BenderBundle/Resources/assets/font/impact.ttf');

                    if(isset($query[1])) {
                        $image->drawTextBox(
                            $query[1],
                            $fontPath,
                            32,
                            10,
                            10,
                            $image->getWidth()-20,
                            $image->getHeight()-20,
                            'top'
                        );
                    }

                    if(isset($query[2])) {
                        $image->drawTextBox(
                            $query[2],
                            $fontPath,
                            32,
                            10,
                            10,
                            $image->getWidth()-20,
                            $image->getHeight()-20,
                            'bottom'
                        );
                    }

                    $image->save($path."/".$filename);

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

        if(strstr($message,'://'))
        {

            return [
                "attachments"=>[
                    [
                        "title"=>"Meme generator",
                        "footer"=> "Meme",
                        "image_url"=>$message,
                        "ts"=> $date->format('U')
                    ]
                ]
            ];
        }else{
            return ['text'=>$message];

        }

    }

}


class ImageProcess {

    private $type;
    private $source;

    public function __construct($filepath){
        $this->source = $this->imageCreateFromAny($filepath);
    }

    public function getWidth()
    {
        return imagesx($this->source);
    }

    public function getHeight()
    {
        return imagesy($this->source);
    }

    function imageCreateFromAny($filepath) {
        $this->type = exif_imagetype($filepath);
        $allowedTypes = array(
            1,  // [] gif
            2,  // [] jpg
            3  // [] png
        );
        if (!in_array($this->type, $allowedTypes)) {
            return false;
        }
        switch ($this->type) {
            case 1 :
                $im = imageCreateFromGif($filepath);
                break;
            case 2 :
                $im = imageCreateFromJpeg($filepath);
                break;
            case 3 :
                $im = imageCreateFromPng($filepath);
                break;
        }
        return isset($im) ? $im : false;
    }

    /**
     * @param $text
     * @param $fontPath
     * @param $size
     * @param $x
     * @param $y
     * @param $width
     * @param $height
     */
    public function drawTextBox($text,$fontPath,$size,$x,$y,$width,$height,$direction="top"){
        $box = new Box($this->source);
        $box->setFontFace($fontPath);
        $box->setFontSize($size);
        $box->setFontColor(new Color(255, 255, 255));
        $box->setBox($x,$y,$width,$height);
        $box->setTextAlign('center', $direction);
        $box->setStrokeColor(new Color(0, 0, 0));
        $box->setStrokeSize(4);
        $box->draw($text);
    }

    /**
     * @param $output
     */
    public function save($output){
        imagejpeg($this->source, $output);
        imagedestroy($this->source);
    }
}

