<?php

/**
 * Class plugin_citation
 */
class plugin_citation extends Plugin
{
    /**
     * @var string
     */
    protected $hook = '!citation';

    /**
     * @param $text
     * @return array
     */
    public function getMessage($text) {
        $citation = include "data/citation.php";
        return $this->array_random($citation);
    }
}

return new plugin_citation();