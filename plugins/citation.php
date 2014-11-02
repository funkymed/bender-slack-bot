<?php

class plugin_citation extends Plugin
{
    protected $hook = '!citation';

    public function getMessage($text) {
        $citation = include "data/citation.php";
        return $this->array_random($citation);
    }
}


return new plugin_citation();