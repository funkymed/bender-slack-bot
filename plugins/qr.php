<?php

class plugin_qr extends Plugin
{
    protected $hook = '!qr';

    public function getMessage($text) {

        global $user_name;

        $t  = explode (' ',$text);
        $qr = include "data/qr.php";
        foreach($qr as $k=>$v)
        {
            foreach($t as $word)
            {
                if(in_array(strtolower($word),explode(',',$k)))
                {
                    $msg = str_replace('%user_name', $user_name, $v);
                    $msg = str_replace('%text', $text, $msg);
                    return $msg;
                }
            }
        }
        return false;
    }

    public function getHelp()
    {
        return false;
    }
}

return new plugin_qr();