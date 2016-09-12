<?php

/**
 * Class Plugin
 */
abstract class Plugin
{
    protected $hook = "";

    abstract protected function getMessage($text);

    /**
     * @return string
     */
    public function getHook()
    {
        return $this->hook;
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

