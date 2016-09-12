<?php

namespace BenderBundle\Service;

/**
 * Class Plugin
 */

abstract class BaseService
{
    protected $hook = "";
    private $data;

    public function __construct(){

    }

    /**
     * Get Message
     * @param $text
     * @return mixed
     */
    abstract protected function getMessage($text);

    /**
     * Get Hook
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
    }

    public function setData($data){
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }

    /**
     * @param     $arr
     * @param int $num
     * @return array
     */
    public function array_random($arr, $num = 1) {
        shuffle($arr);

        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }
        return $num == 1 ? $r[0] : $r;
    }

    /**
     * Get Commands
     * @param $text
     * @return array
     */
    public function getCommands($text)
    {
        $txt = explode(" ",$text);
        $commands = array();
        foreach($txt as $t)
        {
            if($t!=$this->hook)
            {
                $commands[]=$t;
            }
        }
        return $commands;
    }

    /**
     * Get Help
     * @return string
     */
    public function getHelp()
    {
        return $this->getHook();
    }

    /**
     * @param $url
     * @return mixed
     */
    public function get_page_content($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_URL, $url);

        $content = curl_exec($ch);
        curl_close($ch);

        return $content;
    }
}

