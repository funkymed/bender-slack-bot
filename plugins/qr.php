<?php

/**
 * Class plugin_qr
 */
class plugin_qr extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!qr';

    /**
     * @param $text
     * @return bool|mixed
     */
    public function getMessage($text) {

        global $user_name;

        $t  = explode (' ',$text);
        $qr = include "data/qr.php";

        foreach($qr as $k=>$v)
        {
            if(in_array(strtolower($text), explode(',',$k)))
            {
                if(is_array($v))
                    $v = $this->array_random($v);

                $msg = str_replace('%user_name', $user_name, $v);
                $msg = str_replace('%text', $text, $msg);
                return $msg;
            }

            foreach($t as $word)
            {
                if(in_array(strtolower($word),explode(',',$k)))
                {
                    if(is_array($v))
                        $v = $this->array_random($v);

                    $msg = str_replace('%user_name', $user_name, $v);
                    $msg = str_replace('%text', $text, $msg);
                    return $msg;
                }
            }
        }
        return false;
    }

    /**
     * @return bool|string
     */
    public function getHelp()
    {
        return false;
    }
}

return new plugin_qr();