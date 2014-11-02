<?php

class plugin_jcvd extends Plugin
{
    protected $hook = '!jcvd';

    public function getMessage($text) {
        $jcvd = include "data/jcvd.php";
        return $this->array_random($jcvd);
    }
}


return new plugin_jcvd();