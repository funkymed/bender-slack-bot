<?php

/**
 * Class plugin_jcvd
 */
class plugin_jcvd extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!jcvd';

    /**
     * @param $text
     * @return array
     */
    public function getMessage($text) {
        $jcvd = include "data/jcvd.php";
        return $this->array_random($jcvd);
    }
}

return new plugin_jcvd();